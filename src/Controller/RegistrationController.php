<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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


    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function apiRegister(Request $request): JsonResponse
    {
        // Decode JSON data
        $data = json_decode($request->getContent(), true);

        // Validate JSON structure
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate required fields
        $requiredFields = ['prenom', 'nom', 'email', 'password', 'telephone', 'birthday'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return new JsonResponse(['error' => "Field '$field' is required and cannot be empty"], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Invalid email format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate telephone (numeric and minimum length)
        if (!is_numeric($data['telephone']) || strlen($data['telephone']) < 8) {
            return new JsonResponse(['error' => 'Invalid telephone. Must be numeric and at least 8 digits.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Validate birthday (format YYYY-MM-DD)
        if (!\DateTime::createFromFormat('Y-m-d', $data['birthday'])) {
            return new JsonResponse(['error' => 'Invalid birthday format. Expected YYYY-MM-DD.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Check if the user already exists
        $existingUser = $this->entityManager->getRepository(Etudiant::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Email is already in use'], JsonResponse::HTTP_CONFLICT);
        }

        // Create a new user
        $etudiant = new Etudiant();
        $etudiant->setPrenom($data['prenom']);
        $etudiant->setNom($data['nom']);
        $etudiant->setEmail($data['email']);
        $etudiant->setTelephone($data['telephone']);
        $etudiant->setBirthday(new \DateTime($data['birthday']));
        $etudiant->setRoles(['ROLE_USER']);
        $etudiant->setPassword(
            $this->passwordEncoder->hashPassword($etudiant, $data['password'])
        );

        // Persist user
        try {
            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to save user: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Registration successful'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/home', name: 'app_homepage')]
    public function indexC(): Response
    {


        return $this->render('page/user/home.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}