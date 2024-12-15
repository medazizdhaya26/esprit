<?php

namespace App\Controller;
use Symfony\Component\Form\FormError;

use App\Entity\Foyer;
use App\Form\FoyerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboared/foyer')]
final class FoyerController extends AbstractController
{
    #[Route('/', name: 'app_foyer_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer le paramètre de recherche du nom du foyer
    $searchTerm = $request->query->get('search', '');
    $sortBy = $request->query->get('sort_by', 'id');
    $sortOrder = $request->query->get('sort_order', 'asc');

    // Effectuer la recherche et le tri
    $queryBuilder = $entityManager->getRepository(Foyer::class)->createQueryBuilder('f');

    if ($searchTerm) {
        $queryBuilder->where('f.nomFoyer LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');
    }

    if ($sortBy === 'total_chambres') {
        $queryBuilder->addOrderBy('f.nombreChambresSingle + f.nombreChambresDouble', $sortOrder);
    } else {
        $queryBuilder->addOrderBy('f.' . $sortBy, $sortOrder);
    }

    $foyers = $queryBuilder->getQuery()->getResult();

    // Formulaire d'ajout de foyer
    $newFoyer = new Foyer();
    $form = $this->createForm(FoyerType::class, $newFoyer);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        $existingFoyer = $entityManager->getRepository(Foyer::class)->findOneBy([
            'nomFoyer' => $newFoyer->getNomFoyer(),
        ]);

        if ($existingFoyer) {
            $form->get('nomFoyer')->addError(new FormError('Le nom de foyer existe déjà.'));
        } else {
            if ($form->isValid()) {
                $newFoyer->setNombreChambre($newFoyer->getNombreChambresSingle() + $newFoyer->getNombreChambresDouble());
                $entityManager->persist($newFoyer);
                $entityManager->flush();
                $this->addFlash('success', 'Foyer ajouté avec succès!');
                return $this->redirectToRoute('app_foyer_index');
            }
        }
    }

    // Formulaires d'édition
    $editForms = [];
    foreach ($foyers as $foyer) {
        $editForm = $this->createForm(FoyerType::class, $foyer, [
            'action' => $this->generateUrl('app_foyer_edit', ['id' => $foyer->getId()]),
            'method' => 'POST',
        ]);
        $editForms[$foyer->getId()] = $editForm->createView();
    }

    return $this->render('foyer/index.html.twig', [
        'foyers' => $foyers,
        'form' => $form->createView(),
        'form_edit' => $editForms,
        'searchTerm' => $searchTerm,
        'sortBy' => $sortBy,
        'sortOrder' => $sortOrder,  // Pass the sorting parameters to the template
    ]);
}
    #[Route('/{id}', name: 'app_foyer_show', methods: ['GET'])]
    public function show(Foyer $foyer): Response
    {
        return $this->render('foyer/show.html.twig', [
            'foyer' => $foyer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_foyer_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $foyer = $entityManager->getRepository(Foyer::class)->find($id);

        if (!$foyer) {
            throw $this->createNotFoundException('No foyer found for id ' . $id);
        }

        $editForm = $this->createForm(FoyerType::class, $foyer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_foyer_index');
        }

        return $this->redirectToRoute('app_foyer_index');
    }

    #[Route('/delete/{id}', name: 'app_foyer_delete', methods: ['POST'])]
    public function delete(Request $request, Foyer $foyer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $foyer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($foyer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_foyer_index', [], Response::HTTP_SEE_OTHER);
    }
}


