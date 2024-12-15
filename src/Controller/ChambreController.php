<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/chambre')]
class ChambreController extends AbstractController
{
    #[Route('/', name: 'app_chambre_index', methods: ['GET', 'POST'])]
public function index(Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
{    
    


     // Récupérer le paramètre de tri 'disponibilite' (par défaut, on affiche toutes les chambres)
     $sort = $request->query->get('sort', 'all');

     // Filtrer les chambres selon la disponibilité si nécessaire
     if ($sort === 'available') {
         $chambres = $chambreRepository->findBy(['estDisponible' => true]);
     } elseif ($sort === 'occupied') {
         $chambres = $chambreRepository->findBy(['estDisponible' => false]);
     } else {
         // Par défaut, toutes les chambres sont affichées
         $chambres = $chambreRepository->findAll();
     }
 
     // Formulaire d'ajout de chambre
     $newChambre = new Chambre();
     $form = $this->createForm(ChambreType::class, $newChambre);
     $form->handleRequest($request);
 
     if ($form->isSubmitted() && $form->isValid()) {
         $entityManager->persist($newChambre);
         $entityManager->flush();
 
         $this->addFlash('success', 'La chambre a été ajoutée avec succès.');
 
         return $this->redirectToRoute('app_chambre_index');
     }
 
     // Liste des chambres et création des formulaires d'édition
     $editForms = [];
     foreach ($chambres as $chambre) {
         $editForms[$chambre->getId()] = $this->createForm(ChambreType::class, $chambre, [
             'action' => $this->generateUrl('app_chambre_edit', ['id' => $chambre->getId()]),
             'method' => 'POST',
         ])->createView();
     }
 
     return $this->render('chambre/index.html.twig', [
         'chambres' => $chambres,
         'form' => $form->createView(),  // Formulaire d'ajout
         'form_edit' => $editForms,      // Formulaires d'édition
         'currentSort' => $sort,         // Passer la valeur du tri à Twig
     ]);
 }


#[Route('/new', name: 'app_chambre_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
{
    $chambre = new Chambre();
    $form = $this->createForm(ChambreType::class, $chambre);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si une chambre avec le même numéro existe déjà dans ce foyer
        $foyer = $chambre->getFoyer();
        $numeroChambre = $chambre->getNumeroChambre();

        $existingChambre = $chambreRepository->findOneBy([
            'numeroChambre' => $numeroChambre,
            'foyer' => $foyer
        ]);

        if ($existingChambre) {
            // Si une chambre existe déjà avec le même numéro dans ce foyer, ajouter un message flash d'erreur
            $this->addFlash('error', 'Le numéro de chambre ' . $numeroChambre . ' existe déjà dans ce foyer.');
            
            // Renvoyer vers la même page avec les erreurs visibles (sans redirection, juste un retour sur la page)
            return $this->render('chambre/new.html.twig', [
                'chambre' => $chambre,
                'form' => $form->createView(),
            ]);
        }

        // Si le numéro de chambre est unique, persister la nouvelle chambre
        $chambre->setPlacesDisponibles($chambre->getType() === 'double' ? 2 : 1);
        $entityManager->persist($chambre);
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', 'Chambre créée avec succès.');

        return $this->redirectToRoute('app_chambre_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('chambre/new.html.twig', [
        'chambre' => $chambre,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_chambre_show', methods: ['GET'])]
    public function show(Chambre $chambre): Response
    {
        return $this->render('chambre/show.html.twig', [
            'chambre' => $chambre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chambre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chambre $chambre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Chambre modifiée avec succès.');

            return $this->redirectToRoute('app_chambre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chambre/edit.html.twig', [
            'chambre' => $chambre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chambre_delete', methods: ['POST'])]
    public function delete(Request $request, Chambre $chambre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chambre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chambre);
            $entityManager->flush();

            $this->addFlash('success', 'Chambre supprimée avec succès.');
        }

        return $this->redirectToRoute('app_chambre_index', [], Response::HTTP_SEE_OTHER);
    }
}
