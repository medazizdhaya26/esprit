<?php

namespace App\Controller;
use App\Entity\Bibliothecaire;
use App\Entity\Bibliotheque;
use App\Form\BibliothequeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/bibliotheque')]
class BibliothequeController extends AbstractController
{
    #[Route('/', name: 'app_bibliotheque_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bibliotheques = $entityManager->getRepository(Bibliotheque::class)->findAll();
        $newBibliotheque = new Bibliotheque();
        $form = $this->createForm(BibliothequeType::class, $newBibliotheque);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $horairesOuvertureData = $form->get('horairesOuverture')->getData();
                $newBibliotheque->setHorairesOuverture($horairesOuvertureData);
                $entityManager->persist($newBibliotheque);
                $entityManager->flush();

                $this->addFlash('success', 'La bibliothèque a été ajoutée avec succès.');
                return $this->redirectToRoute('app_bibliotheque_index');
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', "Erreur sur le champ '{$error->getOrigin()->getName()}': {$error->getMessage()}");
                }
            }
        }

        $editForms = [];
        foreach ($bibliotheques as $bibliotheque) {
            $editForm = $this->createForm(BibliothequeType::class, $bibliotheque, [
                'action' => $this->generateUrl('app_bibliotheque_edit', ['id' => $bibliotheque->getId()]),
            ]);
            $editForms[$bibliotheque->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/bibliotheque/index.html.twig', [
            'bibliotheques' => $bibliotheques,
            'form' => $form->createView(),
            'form_edit' => $editForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bibliotheque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $bibliotheque = $entityManager->getRepository(Bibliotheque::class)->find($id);
        if (!$bibliotheque) {
            throw $this->createNotFoundException('Aucune bibliothèque trouvée pour l\'ID ' . $id);
        }

        $editForm = $this->createForm(BibliothequeType::class, $bibliotheque);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $horairesOuvertureData = $editForm->get('horairesOuverture')->getData();
                $bibliotheque->setHorairesOuverture($horairesOuvertureData);
                $entityManager->flush();

                $this->addFlash('success', 'La bibliothèque a été modifiée avec succès.');
                return $this->redirectToRoute('app_bibliotheque_index');
            } else {
                foreach ($editForm->getErrors(true) as $error) {
                    $this->addFlash('error', "Erreur sur le champ '{$error->getOrigin()->getName()}': {$error->getMessage()}");
                }
            }
        }

        return $this->render('page/admin/Entity/bibliotheque/edit.html.twig', [
            'bibliotheque' => $bibliotheque,
            'edit_form' => $editForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_bibliotheque_show', methods: ['GET'])]
    public function show(Bibliotheque $bibliotheque): Response
    {
        return $this->render('page/admin/Entity/bibliotheque/show.html.twig', [
            'bibliotheque' => $bibliotheque,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_bibliotheque_delete', methods: ['POST'])]
    public function delete(Request $request, Bibliotheque $bibliotheque, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bibliotheque->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bibliotheque);
            $entityManager->flush();
            $this->addFlash('success', 'La bibliothèque a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression de la bibliothèque.');
        }

        return $this->redirectToRoute('app_bibliotheque_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/bibliothecaires', name: 'app_bibliotheque_bibliothecaires', methods: ['GET'])]
    public function voirBibliothecaires(Bibliotheque $bibliotheque): Response
    {
        $bibliothecaires = $bibliotheque->getBibliothecaires();

        return $this->render('page/admin/Entity/bibliotheque/bibliothecaires.html.twig', [
            'bibliotheque' => $bibliotheque,
            'bibliothecaires' => $bibliothecaires,
        ]);
    }
    #[Route('/{bibliotheque_id}/bibliothecaires/{bibliothecaire_id}/remove', name: 'app_bibliothecaire_remove', methods: ['POST'])]
    public function removeBibliothecaire(
        int $bibliotheque_id,
        int $bibliothecaire_id,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $bibliotheque = $entityManager->getRepository(Bibliotheque::class)->find($bibliotheque_id);
        $bibliothecaire = $entityManager->getRepository(Bibliothecaire::class)->find($bibliothecaire_id);
    
        if (!$bibliotheque || !$bibliothecaire) {
            throw $this->createNotFoundException('Bibliothèque ou bibliothécaire introuvable.');
        }
    
        if ($this->isCsrfTokenValid('delete_bibliothecaire' . $bibliothecaire_id, $request->request->get('_token'))) {
            $bibliotheque->removeBibliothecaire($bibliothecaire);
            $entityManager->flush();
    
            $this->addFlash('success', 'Le bibliothécaire a été dissocié avec succès de la bibliothèque.');
        } else {
            $this->addFlash('error', 'Erreur lors de la dissociation du bibliothécaire.');
        }
    
        return $this->redirectToRoute('app_bibliotheque_show', ['id' => $bibliotheque_id]);
    }
    
}
