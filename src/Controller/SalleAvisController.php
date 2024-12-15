<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\SalleDeSportRepository;
use App\Service\BadWordsFilter as ServiceBadWordsFilter;
use BadWordsFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalleAvisController extends AbstractController
{
    private $entityManager;
    private $badWordsFilter; // Déclarer le service

    public function __construct(EntityManagerInterface $entityManager, ServiceBadWordsFilter $badWordsFilter)
    {
        $this->entityManager = $entityManager;
        $this->badWordsFilter = $badWordsFilter; // Injecter le service
    }

    #[Route('/salle/{id}/avis', name: 'salle_avis')]
    public function ajouterAvis(SalleDeSportRepository $salleRepository, $id, Request $request): Response
    {
        $salle = $salleRepository->find($id);
        
        if (!$salle) {
            throw $this->createNotFoundException('Salle non trouvée');
        }
        
        $form = $this->createForm(AvisType::class, new Avis());
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newAvis = $form->getData();
            $commentaire = $newAvis->getCommentaire();

            // Vérifier si le commentaire contient des mots interdits
            if ($this->badWordsFilter->containsBadWords($commentaire)) {
                $this->addFlash('error', 'Votre avis contient des mots inappropriés et ne sera pas pris en compte.');
                return $this->redirectToRoute('salle_avis', ['id' => $id]); // Rediriger l'utilisateur
            }

            $newAvis->setSalle($salle);

            $user = $this->getUser();
            $pseudo = $user ? $user->getUserIdentifier() : 'Anonyme';
            $newAvis->setPseudo($pseudo);
        
            $this->entityManager->persist($newAvis);
            $this->entityManager->flush();
        
            $this->addFlash('success', 'Votre avis a été ajouté avec succès.');
            return $this->redirectToRoute('salle_avis_afficher', ['id' => $id]);
        }
        
        return $this->render('page/admin/Entity/salle_de_sport/ajouter_avis.html.twig', [
            'form' => $form->createView(),
            'salle' => $salle,
        ]);
    }


    // Cette action affiche tous les avis associés à la salle
    #[Route('/salle/{id}/avis/afficher', name: 'salle_avis_afficher')]
    public function afficherAvis(SalleDeSportRepository $salleRepository, $id): Response
    {
        $salle = $salleRepository->find($id);

        if (!$salle) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        // Récupérer tous les avis associés à la salle
        $avis = $salle->getAvis();

        // Calcul de la moyenne des évaluations
        $moyenne = 0;
        if (count($avis) > 0) {
            $total = 0;
            foreach ($avis as $unAvis) {
                $total += $unAvis->getNote();
            }
            $moyenne = $total / count($avis);
        }

        return $this->render('page/admin/Entity/salle_de_sport/afficher_avis.html.twig', [
            'salle' => $salle,
            'avis' => $avis,
            'moyenne' => $moyenne,
        ]);
    }
}
