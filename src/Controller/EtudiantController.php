<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/Student')]
final class EtudiantController extends AbstractController
{
    #[Route('/', name: 'app_etudiant_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $etudiants = $entityManager->getRepository(Etudiant::class)->findAll();

        return $this->render('page/admin/Entity/etudiant/index.html.twig', [
            'etudiants' => $etudiants,
        ]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/admin/Entity/etudiant/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $etudiant = $entityManager->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            throw $this->createNotFoundException('No student found for id ' . $id);
        }

        $editForm = $this->createForm(EtudiantType::class, $etudiant);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index');
        }

        return $this->render('page/admin/Entity/etudiant/edit.html.twig', [
            'edit_form' => $editForm->createView(),
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('page/admin/Entity/etudiant/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }
}
