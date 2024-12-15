<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Entity\SalleDeSport;
use App\Entity\User;
use App\Form\EvenementType;
use App\Form\EventuserType;
use App\Repository\EvenementRepository;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageController extends AbstractController
{


    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('azizdhaya26@gmail.com')
            ->to('jessem.hamdi147@gmail.com')  // Replace with the recipient's email
            ->subject('Test Email from Symfony')
            ->text('This is a test email sent from Symfony using the Gmail SMTP server.')
            ->html('<p>This is a <strong>test</strong> email sent from Symfony using the Gmail SMTP server.</p>');

        try {
            $mailer->send($email);
            return new Response('Email sent successfully!', 200);
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage(), 500);
        }
    }
    #[Route('/', name: 'app_main_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }

    #[Route('/Contact', name: 'app_page_contact')]
    public function contact(): Response
    {
        return $this->render('page/user/contact.html.twig');
    }
    #[Route('/profile', name: 'app_user_profile')]
    public function user_profile(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Fetch user reservations
        $reservations = $entityManager->getRepository(Reservation::class)->findBy([
            'email' => $user->getUserIdentifier()
        ]);
        $reservedEvents = [];
        foreach ($reservations as $reservation) {
            $event = $reservation->getEvenement();
            array_push($reservedEvents, $event);
        }

        usort($reservedEvents, function ($a, $b) {
            return $a->getDateDebut() <=> $b->getDateDebut();
        });

        // Get user's created events
        $createdEvents = $entityManager->getRepository(Evenement::class)->findBy([
            'responsable_email' => $user->getUserIdentifier()
        ]);

        // Separate validated and non-validated events
        $validatedEvents = [];
        $nonValidatedEvents = [];
        foreach ($createdEvents as $event) {
            if ($event->getValider()) {
                $validatedEvents[] = $event;
            } else {
                $nonValidatedEvents[] = $event;
            }
        }

        usort($validatedEvents, function ($a, $b) {
            return $a->getDateDebut() <=> $b->getDateDebut();
        });

        usort($nonValidatedEvents, function ($a, $b) {
            return $a->getDateDebut() <=> $b->getDateDebut();
        });

        // Fetch user's abonnement
        $abonnement = $entityManager->getRepository(Abonnement::class)->findOneBy([
            'etudiant' => $user->getId() // Assuming 'user' is a foreign key in Abonnement
        ]);

        return $this->render('page/user/profile.html.twig', [
            'validatedEvents' => $validatedEvents,
            'nonValidatedEvents' => $nonValidatedEvents,
            'reservedEvents' => $reservedEvents,
            'abonnement' => $abonnement,
        ]);
    }



    #[Route('/Events/', name: 'app_page_event')]
    public function event(Request $request, EntityManagerInterface $entityManager, HttpClientInterface $client): Response
    {
        $user = $this->getUser();

        $event = new Evenement();
        $event->setValider(false);

        if ($user) {
            $event->setResponsableId($user->getId());
            $event->setResponsableEmail($user->getUserIdentifier());
        }

        $form = $this->createForm(EvenementType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Event created successfully!');

            return $this->redirectToRoute('app_page_event');
        }

        $events = [];
        $recommendations = [];

        if ($user && $user->getUserIdentifier() !== null) {
            try {
                $response = $client->request('GET', 'http://127.0.0.1:5000/get_recommendations', [
                    'query' => ['email' => $user->getUserIdentifier()]
                ]);
                $recommendations = $response->toArray();
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error fetching recommendations from Flask API.');
                $recommendations = [];
            }
        }

        // If recommendations are empty or the user is logged in, fetch events from the database
        if (empty($recommendations)) {
            $currentDate = new DateTime();

            $events = $entityManager->getRepository(Evenement::class)->createQueryBuilder('e')
                ->where('e.valider = :valider')
                ->andWhere('e.date_fin > :currentDate')
                ->setParameter('valider', true)
                ->setParameter('currentDate', $currentDate)
                ->getQuery()
                ->getResult();
        }

        return $this->render('page/user/events/index.html.twig', [
            'events' => $events,
            'form' => $form->createView(),
            'recommendations' => $recommendations // Fixed variable name for recommendations
        ]);
    }

    #[Route('/Club/', name: 'app_page_club')]
    public function club(): Response
    {
        return $this->render('page/user/club/index.html.twig');
    }

    #[Route('/events/{id}', name: 'app_event_details', methods: ['GET'])]
    public function eventDetails(EvenementRepository $repository, int $id): Response
    {
        $event = $repository->find($id);

        return $this->render('page/user/Events/Events_details.html.twig', [
            'event' => $event,
        ]);
    }


    #[Route('/events/{id}/reserve', name: 'app_event_reserve', methods: ['POST'])]
    public function reserveEvent(EvenementRepository $repository, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $repository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You need to be logged in to make a reservation.');
        }
        if ($event->getResponsableEmail() == $user->getUserIdentifier()) {
            $this->addFlash('error', 'You cannot reserve a spot for your own event.');
            return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
        }

        if ($event->getNombreParticipantsMax() !== null && $event->getReservations()->count() >= $event->getNombreParticipantsMax()) {
            $this->addFlash('error', 'The event has reached its maximum participant capacity.');
            return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
        }

        foreach ($event->getReservations() as $existingReservation) {
            if ($existingReservation->getEmail() === $user->getUserIdentifier()) {
                $this->addFlash('error', 'You have already reserved a spot for this event.');
                return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
            }
        }

        $reservation = new Reservation();
        $reservation->setEvenement($event);
        $reservation->setNom($user->getNom());
        $reservation->setEmail($user->getUserIdentifier());
        $reservation->setDateReservation(new \DateTime());

        $event->addReservation($reservation);

        $entityManager->persist($reservation);
        $entityManager->flush();
        $this->addFlash('success', 'You have successfully reserved a spot for this event.');


        return $this->redirectToRoute('app_event_details', ['id' => $event->getId()]);
    }





    #[Route('/dashboared/', name: 'app_page_Dashboared')]
    public function dashboared(): Response
    {
        return $this->render('page/admin/index.html.twig');
    }
    #[Route('/dashboared/Student', name: 'app_Entity_Students')]
    public function Students_Entity(): Response
    {
        return $this->render('page/admin/Entity/etudiant/index.html.twig');
    }
    #[Route('/dashboared/events', name: 'app_events')]
    public function events(): Response
    {
        return $this->render('page/admin/Entity/evenement/index.html.twig');
    }

    #[Route('/salles', name: 'app_page_salle')]
    public function salles(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les salles de sport
        $salles = $entityManager->getRepository(SalleDeSport::class)->findAll();

        return $this->render('page/admin/Entity/salle_de_sport/salleDeSport.html.twig', [
            'salles' => $salles,
        ]);
    }





    #[Route('/salles/{id}', name: 'app_salle_details', methods: ['GET'])]
    public function details(int $id, EntityManagerInterface $em): Response
    {
        $salle = $em->getRepository(SalleDeSport::class)->find($id);

        if (!$salle) {
            throw $this->createNotFoundException('La salle de sport demandée n\'existe pas.');
        }

        return $this->render('page/admin/Entity/salle_de_sport/salleDeSport_details.html.twig', [
            'salle' => $salle,
        ]);
    }





    #[Route('/salles/{id}/abonnement', name: 'app_salle_abonnement')]
    public function abonnement(Request $request, SalleDeSport $salle): Response
    {
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('age', IntegerType::class, ['label' => 'Âge'])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',

                ],
            ])
            ->add('centresInteret', TextType::class, ['label' => 'Centres d\'intérêt en sport'])
            ->add('typeAbonnement', ChoiceType::class, [
                'label' => 'Type d\'abonnement',
                'choices' => [
                    'Mensuel' => 'Mensuel',
                    'Trimestriel' => 'Trimestriel',
                    'Annuel' => 'Annuel',
                ],
                'attr' => ['class' => 'type-abonnement'],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $prix = 0;

            switch ($data['typeAbonnement']) {
                case 'Mensuel':
                    $prix = 50;
                    break;
                case 'Trimestriel':
                    $prix = 150;
                    break;
                case 'Annuel':
                    $prix = 500;
                    break;
            }
            $this->addFlash('success', 'Votre abonnement a été enregistré avec succès.');
            return $this->redirectToRoute('app_page_salle');
        }

        return $this->render('page/admin/Entity/salle_de_sport/abonnement.html.twig', [
            'form' => $form->createView(),
           'salle' => $salle,
        ]);
    }





    #[Route('/produits', name: 'app_produit_index', methods: ['GET'])]
    public function indexProduit(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('page/admin/Entity/produit/Produit.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * Affiche les détails d'un produit spécifique.
     *
     * @param int $id
     * @param ProduitRepository $produitRepository
     * @return Response
     */
    #[Route('/produit/{id}', name: 'produit_details', methods: ['GET'])]
    public function detailsProduit(int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException("Le produit avec l'ID $id n'existe pas.");
        }

        return $this->render('page/admin/Entity/produit/details.html.twig', [
            'produit' => $produit,
        ]);
    }



}
