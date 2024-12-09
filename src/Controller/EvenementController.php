<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboared/evenement')]

 class EvenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET', 'POST'])]
    public function index(
        EvenementRepository $evenementRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('photo_dir')] string $photoDir

    ): Response
    {
        $evenement = new Evenement();
        $evenement->setResponsableEmail('admin@esprit.tn');
        $evenement->setResponsableId(0);
        $evenement->setValider(true);
        $form = $this->createForm(EvenementType::class, $evenement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form['image']->getData();

            if ($image) {
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $evenement->setImage($newFilename);
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index');
        }

        return $this->render('page/admin/Entity/evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement, ReservationRepository $reservationRepository): Response
    {
        // Fetch the reservations for the current event
        $reservations = $reservationRepository->findBy(['evenement' => $evenement]);

        return $this->render('page/admin/Entity/evenement/show.html.twig', [
            'evenement' => $evenement,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement,
                         EntityManagerInterface $entityManager,
                         #[Autowire('photo_dir')] string $photoDir
    ): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();

            if ($image) {
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $evenement->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/admin/Entity/evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/event/validate/{id}', name: 'app_evenement_validate', methods: ['POST'])]
    public function validateEvent($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $event = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$event) {
            return new JsonResponse(['success' => false, 'message' => 'Event not found.'], 404);
        }

        // Set event as validated
        $event->setValider(true);
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
    #[Route('/event/annuler/{id}', name: 'app_evenement_Annuler', methods: ['POST'])]
    public function annulerEvent($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $event = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$event) {
            return new JsonResponse(['success' => false, 'message' => 'Event not found.'], 404);
        }

        $event->setRefuser(true);
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }



    #[Route('/event/update/{id}', name: 'app_evenement_update', methods: ['POST'])]
    public function updateEvent($id, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $event = $entityManager->getRepository(Evenement::class)->find($id);

        if (!$event) {
            return new JsonResponse(['success' => false, 'message' => 'Event not found.'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['start'])) {
            $dateDebut = new \DateTime($data['start']);
            $dateDebut->add(new \DateInterval('P1D'));
            $event->setDateDebut($dateDebut);
        }

        if (isset($data['end'])) {
            $dateFin = new \DateTime($data['end']);
            $event->setDateFin($dateFin);
        }

        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }






}
