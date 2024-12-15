<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboared/Student')]
 class EtudiantController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    #[Route('/', name: 'app_etudiant_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        #[Autowire('photo_dir')] string $photoDir
    ): Response {
        $etudiants = $entityManager->getRepository(Etudiant::class)->findAll();

        // Form for creating a new student
        $newEtudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $newEtudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();
            $newEtudiant->setRoles(['ROLE_USER']);

            if ($image) {
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $newEtudiant->setImage($newFilename);
            }

            $entityManager->persist($newEtudiant);
            $entityManager->flush();
            return $this->redirectToRoute('app_etudiant_index');
        }

        // Forms for editing existing students
        $editForms = [];
        foreach ($etudiants as $etudiant) {
            $editForm = $this->createForm(EtudiantType::class, $etudiant, [
                'action' => $this->generateUrl('app_etudiant_edit', ['id' => $etudiant->getId()]),
                'method' => 'POST',
            ]);
            $editForms[$etudiant->getId()] = $editForm->createView();
        }

        return $this->render('page/admin/Entity/etudiant/index.html.twig', [
            'etudiants' => $etudiants,
            'form' => $form->createView(),
            'form_edit' => $editForms,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_etudiant_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        Etudiant $etudiant,
        #[Autowire('photo_dir')] string $photoDir
    ): Response {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();

            if ($image) {
                if ($etudiant->getImage()) {
                    $oldImagePath = $photoDir . '/' . $etudiant->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Save the new image file
                $newFilename = uniqid() . '.' . $image->guessExtension();
                $image->move($photoDir, $newFilename);
                $etudiant->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index');
        }

        return $this->redirectToRoute('app_etudiant_index');
    }


    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('page/admin/Entity/etudiant/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/ajax/user/search', name: 'ajax_user_search', methods: ['GET'])]


    public function search(Request $request, UserRepository $userRepository): JsonResponse
    {
        $searchTerm = $request->query->get('term', '');

        $users = $userRepository->findBySearchTerm($searchTerm);

        // Log or Debug
        if (empty($users)) {
            return new JsonResponse(['error' => 'No users found'], 404);
        }

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                'telephone' => $user->getTelephone(),
            ];
        }

        return new JsonResponse($data);
    }


}
