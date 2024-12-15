<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/dashboared/products')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();

        $newProduit = new Produit();
        $form = $this->createForm(ProductType::class, $newProduit);
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
                $newProduit->setImage($newFilename); // Stocker le nom du fichier dans la base de données
            }

            $entityManager->persist($newProduit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index');
        }

        $editForms = [];
        foreach ($produits as $produit) {
            $editForm = $this->createForm(ProductType::class, $produit, [
                'action' => $this->generateUrl('admin_produit_edit', ['id' => $produit->getId()]),
                'method' => 'POST',
            ]);
            $editForms[$produit->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/produit/index.html.twig', [
            'produits' => $produits,
            'form' => $form->createView(),
            'form_edit' => $editForms,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_produit_edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'ID ' . $id);
        }

        $editForm = $this->createForm(ProductType::class, $produit);
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
                $produit->setImage($newFilename); // Stocker le nom du fichier dans la base de données
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index');
        }

        return $this->render('page/admin/Entity/produit/edit.html.twig', [
            'produit' => $produit,
            'edit_form' => $editForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('page/admin/Entity/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
