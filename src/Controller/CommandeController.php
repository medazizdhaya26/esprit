<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeAdminType;
use App\Form\CommandeType;
use CommandeAdminType as GlobalCommandeAdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_commande')]
    public function createCommande(Request $request): Response
    {
        $commande = new Commande();
        $commande->setUser($this->getUser());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $total = 100;
            foreach ($commande->getUser()->getPanier()->getProduits() as $produit) {
                $total += $produit->getPrix();
            }
            $commande->setTotal($total);

            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre commande a été enregistrée !');

            return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
        }

        return $this->render('page/admin/Entity/commande/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    #[Route('/commande/confirmation/{id}', name: 'app_commande_confirmation')]
    public function confirmationCommande(int $id): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('page/admin/Entity/commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }
    

    // ADMIN: Liste des commandes
    #[Route('/admin/commandes', name: 'admin_commandes')]
    public function listCommandes(): Response
    {
        $commandes = $this->entityManager->getRepository(Commande::class)->findAll();

        return $this->render('page/admin/Entity/commande/adminCommande.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    // ADMIN: Modifier une commande
    #[Route('/admin/commande/{id}', name: 'admin_commande_edit')]
    public function editCommande(int $id, Request $request): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        $form = $this->createForm(GlobalCommandeAdminType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La commande a été mise à jour avec succès !');

            return $this->redirectToRoute('admin_commandes');
        }

        return $this->render('page/admin/Entity/commande/adminCommande.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }
}

