<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EventuserType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboared/evenement')]

final class EvenementController extends AbstractController
{
    #[Route('/',name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('page/admin/Entity/evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EventuserType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/admin/Entity/evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('page/admin/Entity/evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventuserType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $dateFin->add(new \DateInterval('P1D'));
            $event->setDateFin($dateFin);
        }

        // Persister les changements dans la base de données
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }



}
