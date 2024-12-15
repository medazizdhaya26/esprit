<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ReservChambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: "Veuillez entrer une adresse email valide.")]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\Choice(
        choices: [1, 2, 3, 4, 5],
        message: "Le niveau universitaire doit être compris entre 1 et 5."
    )]
    private ?int $niveauUniversitaire = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(
        choices: ['single', 'double'],
        message: "Le type de chambre doit être 'single' ou 'double'."
    )]
    private ?string $typeChambre = null;

    #[ORM\ManyToOne(targetEntity: Chambre::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chambre $chambre = null;

    #[ORM\ManyToOne(targetEntity: Foyer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Foyer $foyer = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $isValidate = false;

    public function getFoyer(): ?Foyer
    {
        return $this->foyer;
    }

    public function setFoyer(Foyer $foyer): self
    {
        $this->foyer = $foyer;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getNiveauUniversitaire(): ?int
    {
        return $this->niveauUniversitaire;
    }

    public function setNiveauUniversitaire(int $niveauUniversitaire): self
    {
        $this->niveauUniversitaire = $niveauUniversitaire;
        return $this;
    }

    public function getTypeChambre(): ?string
    {
        return $this->typeChambre;
    }

    public function setTypeChambre(string $typeChambre): self
    {
        $this->typeChambre = $typeChambre;
        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): self
    {
        $this->chambre = $chambre;
        return $this;
    }

    public function isValidate(): ?bool
    {
        return $this->isValidate;
    }

    public function setValidate(bool $isValidate): static
    {
        $this->isValidate = $isValidate;

        return $this;
    }
}
