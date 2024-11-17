<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\User;
use App\Form\EventuserType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }

    #[Route('/Contact', name: 'app_page_contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig');
    }
    #[Route('/Events/', name: 'app_page_event')]
    public function event(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();
/*
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to create an event.');
            return $this->redirectToRoute('app_login');
        }*/

        // Create a new Evenement and set default values
        $event = new Evenement();
        $event->setValider(false); 
        $event->setResponsableId($user->getId());
        $event->setResponsableEmail($user->getUserIdentifier());

        // Create and handle the form
        $form = $this->createForm(EventuserType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Event created successfully!');

            return $this->redirectToRoute('app_page_event');
        }

        $events = $entityManager->getRepository(Evenement::class)->findBy(['valider' => true]);

        return $this->render('page/events/index.html.twig', [
            'events' => $events,
            'eventForm' => $form->createView(),
        ]);
    }


    #[Route('/Club/', name: 'app_page_club')]
    public function club(): Response
    {
        return $this->render('page/club/index.html.twig');
    }

    #[Route('/events/{id}', name: 'app_event_details', methods: ['GET'])]
    public function eventDetails(EvenementRepository $repository, int $id): Response
    {
        $event = $repository->find($id);

        return $this->render('page/Events/Events_details.html.twig', [
            'event' => $event,
        ]);
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



}
