<?php
namespace App\Controller;

use App\Entity\Bibliotheque;
use App\Entity\Bibliothecaire;
use App\Form\BibliothecaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/bibliothecaire')]
class BibliothecaireController extends AbstractController
{
    #[Route('/', name: 'app_bibliothecaire_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bibliothecaire = new Bibliothecaire();
        $form = $this->createForm(BibliothecaireType::class, $bibliothecaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Simply set the full email
                $email = $form->get('email')->getData();
                $bibliothecaire->setEmail($email);
    
                $entityManager->persist($bibliothecaire);
                $entityManager->flush();
    
                $this->addFlash('success', 'Le bibliothécaire a été créé avec succès.');
                return $this->redirectToRoute('app_bibliothecaire_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du bibliothécaire.');
            }
        }   

        // Récupération des paramètres de filtrage et tri depuis l'URL
        $filters = [
            'specialisation' => $request->query->get('specialisation'),
            'bibliotheque' => $request->query->get('bibliotheque'),
        ];
        $sort = $request->query->get('sort', 'id');
        $direction = $request->query->get('direction', 'asc');

        // Validation des paramètres de tri
        $allowedFields = ['id', 'nom', 'prenom', 'specialisation'];
        $allowedOrders = ['asc', 'desc'];
        $sort = in_array($sort, $allowedFields) ? $sort : 'id';
        $direction = in_array(strtolower($direction), $allowedOrders) ? strtoupper($direction) : 'ASC';

        // Construction de la requête avec QueryBuilder
        $queryBuilder = $entityManager->getRepository(Bibliothecaire::class)
            ->createQueryBuilder('b');

        // Application des filtres
        foreach ($filters as $field => $value) {
            if ($value) {
                $queryBuilder->andWhere("b.$field = :$field")
                            ->setParameter($field, $value);
            }
        }

        // Application du tri
        $queryBuilder->orderBy("b.$sort", $direction);
        
        // Exécution de la requête
        $bibliothecaires = $queryBuilder->getQuery()->getResult();

        // Récupération des spécialisations distinctes
        $specialisations = $entityManager->getRepository(Bibliothecaire::class)
            ->createQueryBuilder('b')
            ->select('DISTINCT b.specialisation')
            ->getQuery()
            ->getSingleColumnResult();

        // Récupération des bibliothèques
        $bibliotheques = $entityManager->getRepository(Bibliotheque::class)->findAll();

        // Création des formulaires d'édition
        $editForms = [];
        foreach ($bibliothecaires as $existingBibliothecaire) {
            $editForm = $this->createForm(BibliothecaireType::class, $existingBibliothecaire, [
                'action' => $this->generateUrl('app_bibliothecaire_edit', ['id' => $existingBibliothecaire->getId()]),
                'method' => 'POST'
            ]);
            $editForms[$existingBibliothecaire->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/bibliothecaire/index.html.twig', [
            'bibliothecaires' => $bibliothecaires,
            'specialisations' => $specialisations,
            'bibliotheques' => $bibliotheques,
            'form' => $form->createView(),
            'edit_forms' => $editForms,
            'filters' => $filters,
            'currentSort' => $sort,
            'currentDirection' => $direction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bibliothecaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bibliothecaire $bibliothecaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BibliothecaireType::class, $bibliothecaire);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Gérer l'email
                $email = $form->get('email')->getData();
                $bibliothecaire->setEmail($email);
    
                // Capitaliser les champs
                $this->capitalizeFields($bibliothecaire);
    
                // Gérer l'upload d'image
                $this->handleImageUpload($form, $bibliothecaire, $bibliothecaire->getImage());
    
                $entityManager->flush();
                $this->addFlash('success', 'Le bibliothécaire a été modifié avec succès.');
    
                return $this->redirectToRoute('app_bibliothecaire_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification.');
            }
        }
    
        return $this->redirectToRoute('app_bibliothecaire_index');
    }

    #[Route('/{id}', name: 'app_bibliothecaire_show', methods: ['GET'])]
    public function show(Bibliothecaire $bibliothecaire): Response
    {
        return $this->render('page/admin/Entity/bibliothecaire/show.html.twig', [
            'bibliothecaire' => $bibliothecaire,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_bibliothecaire_delete', methods: ['POST'])]
    public function delete(Request $request, Bibliothecaire $bibliothecaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bibliothecaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bibliothecaire);
            $entityManager->flush();
            $this->addFlash('success', 'Le bibliothécaire a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression du bibliothécaire.');
        }

        return $this->redirectToRoute('app_bibliothecaire_index', [], Response::HTTP_SEE_OTHER);
    }

    private function capitalizeFields(Bibliothecaire $bibliothecaire): void
    {
        $bibliothecaire->setNom(ucfirst(strtolower($bibliothecaire->getNom())));
        $bibliothecaire->setPrenom(ucfirst(strtolower($bibliothecaire->getPrenom())));
        $bibliothecaire->setSpecialisation(ucfirst(strtolower($bibliothecaire->getSpecialisation())));
    }

    private function handleImageUpload($form, $bibliothecaire, $oldImage = null)
    {
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            if ($oldImage) {
                $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageFileName = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('images_directory'),
                $imageFileName
            );

            $bibliothecaire->setImage($imageFileName);
        } elseif ($oldImage) {
            $bibliothecaire->setImage($oldImage);
        }
    }
}