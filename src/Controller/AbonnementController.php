<?php

namespace App\Controller;

use AbonnementType as GlobalAbonnementType;
use App\Entity\Abonnement;
use App\Form\AbonnementType;  // Correctement importé
use App\Repository\AbonnementRepository;
use App\Entity\SalleDeSport;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abonnement')]
final class AbonnementController extends AbstractController
{
    #[Route(name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        // Récupère l'utilisateur connecté (si nécessaire)
        $etudiant = $this->getUser();
        
        if (!$etudiant) {
            // Si l'utilisateur n'est pas connecté, tu peux soit rediriger soit afficher une erreur
            return $this->redirectToRoute('app_login');
        }

        // Trouve tous les abonnements associés à l'utilisateur
        $abonnements = $abonnementRepository->findBy(['etudiant' => $etudiant]);

        return $this->render('page/admin/Entity/abonnement/index.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }

    #[Route('/new/{id}', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'étudiant connecté
        $etudiant = $this->getUser(); // Cette ligne récupère l'utilisateur connecté (étudiant)

        // Vérifiez si l'étudiant est connecté
        if (!$etudiant) {
            // Si l'étudiant n'est pas connecté, redirigez-le vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Créer un nouvel abonnement
        $abonnement = new Abonnement();
        
        // Associer l'étudiant à l'abonnement
        $abonnement->setEtudiant($etudiant);

        // Récupérer une salle de sport (par exemple avec l'ID 2 ici)
        $salle = $entityManager->getRepository(SalleDeSport::class)->find($id);

        if (!$salle) {
            throw $this->createNotFoundException('Salle non trouvée.');
        }

        // Ajouter d'autres données si nécessaire, par exemple l'ID de la salle à l'abonnement
        // $abonnement->setSalleDeSport($salle); // Si vous souhaitez associer la salle à l'abonnement

        // Créer et traiter le formulaire d'abonnement
        $form = $this->createForm(AbonnementType::class, $abonnement);  // Utilisation correcte du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister l'abonnement en base de données
            $entityManager->persist($abonnement);
            $entityManager->flush();
            
            // Rediriger vers une page de succès après la soumission
            return $this->redirectToRoute('app_abonnement_success');
        }

        return $this->render('page/admin/Entity/abonnement/new.html.twig', [
            'form' => $form->createView(),
            'salle' => $salle,
        ]);
    }

    #[Route('/success', name: 'app_abonnement_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('page/admin/Entity/abonnement/success.html.twig');
    }

    #[Route('/{id}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('page/admin/Entity/abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('page/admin/Entity/abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->get('token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
