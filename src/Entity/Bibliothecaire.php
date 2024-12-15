<?php
namespace App\Entity;

use App\Repository\BibliothecaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BibliothecaireRepository::class)]
class Bibliothecaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    private ?string $prenom = null;

    // #[ORM\Column(length: 255)]
    // #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire.")]
    // private ?string $emailUser = null; // Partie avant le '@'

    // #[ORM\Column(length: 255)]
    // #[Assert\NotBlank(message: "Le domaine de l'email est obligatoire.")]
    // private ?string $emailDomain = '@esprit.tn'; // Domaine par défaut

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: "Veuillez fournir un email valide.")]
    private ?string $email = null; // Email complet (calculé)

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d{8,11}$/",
        message: "Le numéro de téléphone doit contenir uniquement des chiffres et être composé de 8 à 11 caractères."
    )]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $adresse = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThan('today')]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: "array")]
    #[Assert\NotNull(message: "Les compétences doivent être renseignées.")]
    private array $competences = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La spécialisation est obligatoire.")]
    private ?string $specialisation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: Bibliotheque::class, inversedBy: 'bibliothecaires')]
    private ?Bibliotheque $bibliotheque = null;

    // Getter et setter pour email complet
    // public function getEmail(): ?string
    // {
    //     return $this->emailUser . $this->emailDomain; // Génère l'email complet
    // }

    // private function updateEmail(): void
    // {
    //     $this->email = $this->getEmail(); // Met à jour l'email complet
    // }

    // Getters et setters pour les autres champs
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

    // public function getEmailUser(): ?string
    // {
    //     return $this->emailUser;
    // }

    // public function setEmailUser(string $emailUser): static
    // {
    //     $this->emailUser = $emailUser;
    //     $this->updateEmail();
    //     return $this;
    // }

    // public function getEmailDomain(): ?string
    // {
    //     return $this->emailDomain;
    // }

    // public function setEmailDomain(string $emailDomain): static
    // {
    //     $this->emailDomain = $emailDomain;
    //     $this->updateEmail();
    //     return $this;
    // }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
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

    public function getCompetences(): array
    {
        return $this->competences;
    }

    public function setCompetences(array $competences): static
    {
        $this->competences = $competences;
        return $this;
    }

    public function getSpecialisation(): ?string
    {
        return $this->specialisation;
    }

    public function setSpecialisation(string $specialisation): static
    {
        $this->specialisation = $specialisation;
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

    public function getBibliotheque(): ?Bibliotheque
    {
        return $this->bibliotheque;
    }

    public function setBibliotheque(?Bibliotheque $bibliotheque): static
    {
        $this->bibliotheque = $bibliotheque;
        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
}
