<?php
namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/Livre')]
final class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les paramètres de filtrage depuis la requête
        $categorie = $request->query->get('categorie'); // Filtrer par catégorie
        $auteur = $request->query->get('auteur');       // Filtrer par auteur
        $langue = $request->query->get('langue');       // Filtrer par langue
        $stock = $request->query->get('stock');

        // Créer un QueryBuilder pour appliquer les filtres
        $queryBuilder = $entityManager->getRepository(Livre::class)->createQueryBuilder('l');

        // Ajouter les filtres dynamiques
        if ($categorie) {
            $queryBuilder->andWhere('l.categorie = :categorie')
                         ->setParameter('categorie', $categorie);
        }

        if ($auteur) {
            $queryBuilder->andWhere('l.auteur = :auteur')
                         ->setParameter('auteur', $auteur);
        }

        if ($langue) {
            $queryBuilder->andWhere('l.langue = :langue')
                         ->setParameter('langue', $langue);
        }

        if ($stock) {if ($stock === 'disponible') {
            $queryBuilder->andWhere('l.stock > 0');
        } elseif ($stock === 'non_disponible') {
            $queryBuilder->andWhere('l.stock = 0');
        }}

        // Récupérer les livres filtrés
        $livres = $queryBuilder->getQuery()->getResult();
        // Appliquer ucfirst() sur les titres, auteurs et catégories
        foreach ($livres as $livre) {
            $livre->setTitre(ucwords(strtolower($livre->getTitre())));  // Capitaliser le titre
            $livre->setAuteur(ucwords(strtolower($livre->getAuteur()))); // Capitaliser l'auteur
            $livre->setCategorie(ucwords(strtolower($livre->getCategorie())));
            $livre->setLangue(ucwords(strtolower($livre->getLangue()))); // Capitaliser la catégorie
        }

        // Récupérer toutes les catégories, auteurs et langues pour les options de filtre
        $categories = $entityManager->getRepository(Livre::class)
            ->createQueryBuilder('l')
            ->select('DISTINCT l.categorie')
            ->getQuery()
            ->getSingleColumnResult();

        $auteurs = $entityManager->getRepository(Livre::class)
            ->createQueryBuilder('l')
            ->select('DISTINCT l.auteur')
            ->getQuery()
            ->getSingleColumnResult();

        $langues = $entityManager->getRepository(Livre::class)
            ->createQueryBuilder('l')
            ->select('DISTINCT l.langue')
            ->getQuery()
            ->getSingleColumnResult();

        // Création d'un nouveau livre
        $newLivre = new Livre();
        $form = $this->createForm(LivreType::class, $newLivre);
        $form->handleRequest($request);

        // Créer un formulaire d'édition pour chaque livre
        $formEdit = [];
        foreach ($livres as $livre) {
            $formEdit[$livre->getId()] = $this->createForm(LivreType::class, $livre, [
                'action' => $this->generateUrl('app_livre_edit', ['id' => $livre->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Ajouter des validations personnalisées
                if (!$this->validateLivre($newLivre)) {
                    // Si la condition échoue, rediriger vers la page d'édition
                    $this->addFlash('error', 'Le livre ne respecte pas les conditions requises.');
                    return $this->redirectToRoute('app_livre_edit', ['id' => $newLivre->getId()]);
                }

                // Si la validation passe, persister le livre
                $entityManager->persist($newLivre);
                $entityManager->flush();
                $this->addFlash('success', 'Le livre a été ajouté avec succès.');
                return $this->redirectToRoute('app_livre_index');
            } else {
                // Ajouter les erreurs de validation à la session
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', "Erreur sur le champ '{$error->getOrigin()->getName()}': {$error->getMessage()}");
                }
            }
        }

        return $this->render('page/admin/Entity/livre/index.html.twig', [
            'livres' => $livres,
            'categories' => $categories,
            'auteurs' => $auteurs,
            'langues' => $langues,
            'form' => $form->createView(),
            'currentCategorie' => $categorie,
            'currentAuteur' => $auteur,
            'currentLangue' => $langue,
            'form_edit' => $formEdit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer le livre à modifier
        $livre = $entityManager->getRepository(Livre::class)->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Aucun livre trouvé pour l\'id ' . $id);
        }

        // Création du formulaire d'édition
        $editForm = $this->createForm(LivreType::class, $livre);
        $editForm->handleRequest($request);

        // Vérification du formulaire
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                // Traitement de la catégorie
                $categorie = $editForm->get('categorie')->getData();
                if ($categorie) {
                    $livre->setCategorie($categorie);
                }

                // Gestion de la langue (si nécessaire)
                $langue = $editForm->get('langue')->getData();
                if ($langue) {
                    $livre->setLangue($langue);
                }

                // Enregistrement des modifications
                $entityManager->flush(); // Sauvegarde les modifications en base

                // Message de succès
                $this->addFlash('success', 'Le livre a été modifié avec succès.');
                return $this->redirectToRoute('app_livre_index');
            } else {
                // Ajouter les erreurs de validation à la session
                foreach ($editForm->getErrors(true) as $error) {
                    $this->addFlash('error', "Erreur sur le champ '{$error->getOrigin()->getName()}': {$error->getMessage()}");
                }
            }
        }

        // Afficher le formulaire d'édition
        return $this->render('page/admin/Entity/livre/edit.html.twig', [
            'livre' => $livre,
            'edit_form' => $editForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    { //DATE tjrs superieur a aujourd'hui avec diff un mois au maximum
        $availabilityDate = null;
        if ($livre->getStock() === 0) {
            $today = new \DateTime();
            $randomDays = rand(1, 30); 
            $availabilityDate = (clone $today)->modify("+{$randomDays} days");
        }
    
        return $this->render('page/admin/Entity/livre/show.html.twig', [
            'livre' => $livre,
            'availabilityDate' => $availabilityDate,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($livre);
                $entityManager->flush(); // Suppression en base
                $this->addFlash('success', 'Le livre a été supprimé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression du livre : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Le token CSRF est invalide.');
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }

    // Validation personnalisée
    private function validateLivre(Livre $livre): bool
    {
        // Exemple de condition personnalisée : si le titre est vide, renvoyer false
        if (empty($livre->getTitre())) {
            return false;  // Condition non respectée
        }

        // Ajouter d'autres validations personnalisées si nécessaire
        return true;  // Si toutes les conditions sont respectées
    }
    #[Route('/delete_all', name: 'app_livre_delete_all', methods: ['DELETE'])]
    public function deleteAll(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les livres
        $livres = $entityManager->getRepository(Livre::class)->findAll();

        if ($livres) {
            foreach ($livres as $livre) {
                $entityManager->remove($livre);  // Supprimer chaque livre
            }
            $entityManager->flush();  // Appliquer les suppressions
        }

        // Retourner une réponse JSON ou un message de succès
        return $this->json(['status' => 'success', 'message' => 'Tous les livres ont été supprimés']);
    }
}
