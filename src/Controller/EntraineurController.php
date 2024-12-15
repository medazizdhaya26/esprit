<?php

namespace App\Controller;

use App\Entity\Entraineur;
use App\Form\EntraineurType;
use App\Repository\EntraineurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/dashboared/Entraineur')]
final class EntraineurController extends AbstractController
{
    #[Route('/', name: 'app_entraineur_index', methods: ['GET', 'POST'])]
public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, EntraineurRepository $entraineurRepository): Response
{
    // Récupère les valeurs des critères de recherche depuis la requête
    $searchSpecialite = $request->query->get('specialite', '');
    $searchSalle = $request->query->get('salle', '');

    // Recherche des entraîneurs selon les critères de spécialité et salle
    $entraineurs = $entraineurRepository->searchBySpecialiteEtSalle($searchSpecialite, $searchSalle);

    // Formulaire pour ajouter un nouvel entraîneur
    $newEntraineur = new Entraineur();
    $form = $this->createForm(EntraineurType::class, $newEntraineur, [
        'action' => $this->generateUrl('app_entraineur_index'),
    ]);
    $form->handleRequest($request);

    // Traitement du formulaire si soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Gestion de l'image (si présente)
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('images_directory'), $newFilename);
            $newEntraineur->setImage($newFilename);
        }

        // Sauvegarde de l'entraîneur
        $entityManager->persist($newEntraineur);
        $entityManager->flush();

        return $this->redirectToRoute('app_entraineur_index');
    }

    // Formulaires d'édition pour chaque entraîneur
    $editForms = [];
    foreach ($entraineurs as $entraineur) {
        $editForm = $this->createForm(EntraineurType::class, $entraineur, [
            'action' => $this->generateUrl('app_entraineur_edit', ['id' => $entraineur->getId()]),
            'method' => 'POST',
        ]);
        $editForms[$entraineur->getId()] = $editForm->createView();
    }

    // Retourner la vue avec la liste d'entraîneurs et les formulaires
    return $this->render('page/admin/Entity/entraineur/index.html.twig', [
        'entraineurs' => $entraineurs,
        'form' => $form->createView(),
        'form_edit' => $editForms,
        'searchSpecialite' => $searchSpecialite,
        'searchSalle' => $searchSalle,
    ]);
}


    #[Route('/{id}/edit', name: 'app_entraineur_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id, SluggerInterface $slugger): Response
    {
        $entraineur = $entityManager->getRepository(Entraineur::class)->find($id);

        if (!$entraineur) {
            throw $this->createNotFoundException('No trainer found for id ' . $id);
        }

        $editForm = $this->createForm(EntraineurType::class, $entraineur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $editForm->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename); // Utiliser le Slugger pour sécuriser le nom
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacer le fichier dans le dossier public/uploads/images
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Mettre à jour l'entraineur avec le nom du fichier
                $entraineur->setImage($newFilename);
            }

            // Sauvegarder les modifications
            $entityManager->flush();
            return $this->redirectToRoute('app_entraineur_index');
        }

        return $this->redirectToRoute('app_entraineur_index');
    }

    #[Route('/delete/{id}', name: 'app_entraineur_delete', methods: ['POST'])]
    public function delete(Request $request, Entraineur $entraineur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $entraineur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entraineur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entraineur_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_entraineur_show', methods: ['GET'])]
    public function show(Entraineur $entraineur): Response
    {
        return $this->render('page/admin/Entity/entraineur/show.html.twig', [
            'entraineur' => $entraineur,
        ]);
    }
}
