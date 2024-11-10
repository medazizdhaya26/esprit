<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    #[Route('/registration', name: 'app_regestration')]
    public function indexC(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(UserFormType::class, $etudiant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->setPassword(
                $this->passwordEncoder->hashPassword($etudiant, $etudiant->getPassword())
            );

            // Set their role
            $etudiant->setRoles(['ROLE_USER']);

            // Save user
            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_page_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
