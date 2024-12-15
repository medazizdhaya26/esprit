<?php

namespace App\Entity;

use App\Repository\EntraineurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntraineurRepository::class)]
class Entraineur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
#[Assert\NotBlank(message: "Le nom est obligatoire.")]
#[Assert\Length(
    max: 255,
    maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
)]
#[Assert\Regex(
    pattern: "/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/u",
    message: "Le nom ne peut contenir que des lettres."
)]

    
    private ?string $nom = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Veuillez fournir un email valide.")]
    private ?string $email = null;

    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]{8,11}$/",
        message: "Le numéro de téléphone se compose de 8 à 11 chiffres."
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $adresse = null;

    
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThan(
        'today',
        message: "La date de naissance doit être dans le passé."
    )]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: "array")]
   
    private ?array $qualifications = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La specialite est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La specialite ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $specialite = null;

    #[ORM\ManyToOne(targetEntity: SalleDeSport::class, inversedBy: 'entraineurs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SalleDeSport $salle = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Image(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG ou PNG)."
    )]
    private ?string $image = null;

    public function __construct()
    {
        // Initialisation des qualifications à un tableau vide
        $this->qualifications = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getQualifications(): ?array
    {
        return $this->qualifications;
    }

    public function setQualifications(array $qualifications): static
    {
        $this->qualifications = $qualifications;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getSalle(): ?SalleDeSport
    {
        return $this->salle;
    }

    public function setSalle(?SalleDeSport $salle): static
    {
        $this->salle = $salle;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    
}
