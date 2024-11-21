<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginFormAuthenticator;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    #[Route('/home', name: 'app_homepage')]
    public function indexC(Request $request, AuthenticationUtils $authenticationUtils, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): Response
    {
        $etudiant = new Etudiant();
        // Generate the registration form
        $form = $this->createForm(UserFormType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->setPassword(
                $this->passwordEncoder->hashPassword($etudiant, $etudiant->getPassword())
            );
            $etudiant->setRoles(['ROLE_USER']);
            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_homepage');
        }

        // Render the registration form
        return $this->render('page/user/home.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Controller for logout - the actual logout is handled by Symfony, this just defines the route
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}