<?php

namespace App\Controller;

use App\Entity\SalleDeSport;
use App\Form\SalleDeSportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/SalleDeSport')]
class SalleDeSportController extends AbstractController
{
    #[Route('/', name: 'app_salle_de_sport_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salles = $entityManager->getRepository(SalleDeSport::class)->findAll();

        $newSalle = new SalleDeSport();
        $form = $this->createForm(SalleDeSportType::class, $newSalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du fichier image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                // Déplacez le fichier dans le répertoire 'uploads/images'
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/images',
                    $newFilename
                );
                $newSalle->setImage($newFilename); // Stocker le nom du fichier dans la base de données
            }

            $entityManager->persist($newSalle);
            $entityManager->flush();

            return $this->redirectToRoute('app_salle_de_sport_index');
        }

        $editForms = [];
        foreach ($salles as $salle) {
            $editForm = $this->createForm(SalleDeSportType::class, $salle, [
                'action' => $this->generateUrl('app_salle_de_sport_edit', ['id' => $salle->getId()]),
                'method' => 'POST',
            ]);
            $editForms[$salle->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/salle_de_sport/index.html.twig', [
            'salles' => $salles,
            'form' => $form->createView(),
            'form_edit' => $editForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salle_de_sport_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $salle = $entityManager->getRepository(SalleDeSport::class)->find($id);

        if (!$salle) {
            throw $this->createNotFoundException('Aucune salle de sport trouvée pour l\'ID ' . $id);
        }

        $editForm = $this->createForm(SalleDeSportType::class, $salle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Gestion du fichier image
            /** @var UploadedFile $imageFile */
            $imageFile = $editForm->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                // Déplacez le fichier dans le répertoire 'uploads/images'
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/images',
                    $newFilename
                );
                $salle->setImage($newFilename); // Stocker le nom du fichier dans la base de données
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_salle_de_sport_index');
        }

        return $this->render('page/admin/Entity/salle_de_sport/edit.html.twig', [
            'salle' => $salle,
            'edit_form' => $editForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_salle_de_sport_show', methods: ['GET'])]
    public function show(SalleDeSport $salle): Response
    {
        return $this->render('page/admin/Entity/salle_de_sport/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_salle_de_sport_delete', methods: ['POST'])]
    public function delete(Request $request, SalleDeSport $salle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($salle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_salle_de_sport_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/entraineurs', name: 'app_salle_de_sport_entraineurs', methods: ['GET'])]
    public function voirEntraineurs(SalleDeSport $salle): Response
    {
        // Récupérer les entraîneurs liés à cette salle via la relation OneToMany
        $entraineurs = $salle->getEntraineurs();

        return $this->render('page/admin/Entity/entraineur/entraineurs_par_salle.html.twig', [
            'salle' => $salle,
            'entraineurs' => $entraineurs,
        ]);
    }
} 

