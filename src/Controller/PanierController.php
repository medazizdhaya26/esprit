<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\user;
use App\Entity\TestPanierProduit; // Assurez-vous que cette entité existe
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{

    #[Route('/panier', name: 'panier_afficher')]
    public function afficherPanier(EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();

        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour voir votre panier.');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $utilisateur]);

        if ($panier) {
            $prixTotal = 0;
            foreach ($panier->getProduits() as $TestPanierProduit) {
                $prixTotal += $TestPanierProduit->getProduit()->getPrix() * $TestPanierProduit->getQuantite();
            }
            $panier->setPrixTotal($prixTotal);
            $entityManager->flush();
        }

        return $this->render('page/admin/Entity/panier/index.html.twig', [
           'panier' => $panier,
        ]);
    }
    /**
     * Ajouter une quantité d'un produit dans le panier.
     */
    #[Route('/panier/ajouter_quantite/{id}', name: 'panier_ajouter_quantite')]
    public function ajouterQuantite(Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre panier.');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $utilisateur]);
        if (!$panier) {
            $panier = new Panier();
            if (isset($utilisateur)) {
                $panier->setUser($utilisateur) ;
            }
            $entityManager->persist($panier);
        }

        $TestPanierProduit = $entityManager->getRepository(TestPanierProduit::class)->findOneBy([
            //'panier' => $panier,
            'produit' => $produit,
        ]);

        if ($TestPanierProduit) {
            $TestPanierProduit->setQuantite($TestPanierProduit->getQuantite() + 1);
        } else {
            $TestPanierProduit = new TestPanierProduit();
            $TestPanierProduit->setPanier($panier)
                          ->setProduit($produit)
                          ->setQuantite(1);
            $panier->addTestPanierProduit($TestPanierProduit);
        }

        $entityManager->flush();

        return $this->redirectToRoute('panier_afficher');
    }

    /**
     * Réduire la quantité d'un produit dans le panier.
     */
    #[Route('/panier/reduire_quantite/{id}', name: 'panier_reduire_quantite')]
    public function reduireQuantite(Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
    {
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $utilisateur]);

        if ($panier) {
            $TestPanierProduit = $entityManager->getRepository(TestPanierProduit::class)->findOneBy([
                'panier' => $panier,
                'produit' => $produit,
            ]);

            if ($TestPanierProduit) {
                $quantite = $TestPanierProduit->getQuantite();

                if ($quantite > 1) {
                    $TestPanierProduit->setQuantite($quantite - 1);
                } else {
                    $panier->removeTestPanierProduit($TestPanierProduit);
                }
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_afficher');
    }

    /**
     * Supprimer un produit du panier.
     */
    #[Route('/panier/supprimer/{id}', name: 'panier_supprimer', methods: ['POST'])]
    public function supprimerProduit(Produit $produit, EntityManagerInterface $entityManager): RedirectResponse
    {
        $utilisateur = $this->getUser();

        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier votre panier.');
            return $this->redirectToRoute('app_login');
        }

        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $utilisateur]);

        if ($panier) {
            $TestPanierProduit = $entityManager->getRepository(TestPanierProduit::class)->findOneBy([
                'panier' => $panier,
                'produit' => $produit,
            ]);

            if ($TestPanierProduit) {
                $panier->removeTestPanierProduit($TestPanierProduit);
                $entityManager->flush();

                $this->addFlash('success', 'Produit supprimé du panier.');
            }
        }

        return $this->redirectToRoute('panier_afficher');
    }
}
