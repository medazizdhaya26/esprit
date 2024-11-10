<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexC(Request $request, AuthenticationUtils $authenticationUtils, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(UserFormType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etudiant->setPassword(
                $this->passwordEncoder->hashPassword($etudiant, $etudiant->getPassword())
            );

            $etudiant->setRoles(['ROLE_USER']);

            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();

            // Authenticate and redirect user after registration
            $response = $userAuthenticator->authenticateUser(
                $etudiant,
                $authenticator,
                $request
            );

            // Redirect to dashboard if user has admin role after authentication
            if ($etudiant->getRoles() && in_array('ROLE_ADMIN', $etudiant->getRoles(), true)) {
                return $this->redirectToRoute('app_page_Dashboared');
            }

            return $response;
        }

        $error = $authenticationUtils->getLastAuthenticationError() ?? null;
        $lastUsername = $authenticationUtils->getLastUsername() ?? '';

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_page_Dashboared');
        }

        return $this->render('page/home.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('');
    }


}
