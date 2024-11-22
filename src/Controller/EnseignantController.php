<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Form\EnseignantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/dashboared/Enseignant')]
class EnseignantController extends AbstractController
{

    #[Route('/', name: 'app_enseignant_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('photo_dir')] string $photoDir,
        UserPasswordHasherInterface $passwordHasher // Inject the password hasher
    ): Response {
        $enseignants = $entityManager->getRepository(Enseignant::class)->findAll();

        $newEnseignant = new Enseignant();
        $newEnseignant->setRoles(['ROLE_USER']);

        $form = $this->createForm(EnseignantType::class, $newEnseignant);
        $form->handleRequest($request);
        $formHasErrors = $form->isSubmitted() && !$form->isValid();

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $image = $form['image']->getData();
            if ($image) {
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $newEnseignant->setImage($newFilename);
            }

            // Hash the password
            $plainPassword = $newEnseignant->getPassword();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($newEnseignant, $plainPassword);
                $newEnseignant->setPassword($hashedPassword);
            }

            $entityManager->persist($newEnseignant);
            $entityManager->flush();
            return $this->redirectToRoute('app_enseignant_index');
        }

        $editForms = [];
        foreach ($enseignants as $enseignant) {
            $editForm = $this->createForm(EnseignantType::class, $enseignant, [
                'action' => $this->generateUrl('app_enseignant_edit', ['id' => $enseignant->getId()]),
                'method' => 'POST',
            ]);
            $editForms[$enseignant->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/enseignant/index.html.twig', [
            'professors' => $enseignants,
            'form' => $form->createView(),
            'form_edit' => $editForms,
            'formHasErrors' => $formHasErrors,
        ]);
    }


    #[Route('/edit/{id}', name: 'app_enseignant_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        Enseignant $enseignant,
        #[Autowire('photo_dir')] string $photoDir
    ): Response {
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();

            if ($image) {
                if ($enseignant->getImage()) {
                    $oldImagePath = $photoDir . '/' . $enseignant->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Save the new image file
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $enseignant->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_index');
        }

        return $this->redirectToRoute('app_enseignant_index');
    }

    #[Route('/{id}', name: 'app_enseignant_show', methods: ['GET'])]
    public function show(Enseignant $enseignant): Response
    {
        return $this->render('page/admin/Entity/enseignant/show.html.twig', [
            'enseignant' => $enseignant,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_enseignant_delete', methods: ['POST'])]
    public function delete(Request $request, Enseignant $enseignant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $enseignant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($enseignant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enseignant_index', [], Response::HTTP_SEE_OTHER);
    }
}