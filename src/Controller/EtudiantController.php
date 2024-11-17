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
    #[Route('/', name: 'app_etudiant_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiants = $entityManager->getRepository(Etudiant::class)->findAll();

        $newEtudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $newEtudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($newEtudiant);
            $entityManager->flush();
            return $this->redirectToRoute('app_etudiant_index');
        }

        $editForms = [];
        foreach ($etudiants as $etudiant) {
            $editForm = $this->createForm(EtudiantType::class, $etudiant, [
                'action' => $this->generateUrl('app_etudiant_edit', ['id' => $etudiant->getId()]),
                'method' => 'POST',
            ]);
            $editForms[$etudiant->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/etudiant/index.html.twig', [
            'etudiants' => $etudiants,
            'form' => $form->createView(),
            'form_edit' => $editForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['POST'])]
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

        return $this->redirectToRoute('app_etudiant_index');
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
