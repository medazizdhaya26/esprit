<?php
namespace App\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\ChambreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChambreRepository::class)]
#[UniqueEntity(
    fields: ['numeroChambre', 'foyer'],
    message: 'Ce numéro de chambre existe déjà dans ce foyer.'
)]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Regex(
        pattern: "/^\d{2}$/", 
        message: "Le numéro de chambre doit être composé de deux chiffres uniquement "
    )]
    private ?string $numeroChambre = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $estDisponible = true;

    #[ORM\Column]
    private ?bool $climatiseur = false;

    #[ORM\Column]
    private ?bool $chauffageCentral = false;

    #[ORM\ManyToOne(targetEntity: Foyer::class, inversedBy: 'chambres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Foyer $foyer = null;

    #[ORM\Column]
    private ?int $PlacesDisponibles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroChambre(): ?string
    {
        return $this->numeroChambre;
    }

    public function setNumeroChambre(string $numeroChambre): self
    {
        $this->numeroChambre = $numeroChambre;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
         // Automatically set placesDisponibles based on type
    if ($type === 'double') {
        $this->PlacesDisponibles = 2;
    } elseif ($type === 'single') {
        $this->PlacesDisponibles = 1;
    }

        return $this;
    }

    public function isEstDisponible(): ?bool
    {
        return $this->estDisponible;
    }

    public function setEstDisponible(bool $estDisponible): self
    {
        $this->estDisponible = $estDisponible;

        return $this;
    }

    public function isClimatiseur(): ?bool
    {
        return $this->climatiseur;
    }

    public function setClimatiseur(bool $climatiseur): self
    {
        $this->climatiseur = $climatiseur;

        return $this;
    }

    public function isChauffageCentral(): ?bool
    {
        return $this->chauffageCentral;
    }

    public function setChauffageCentral(bool $chauffageCentral): self
    {
        $this->chauffageCentral = $chauffageCentral;

        return $this;
    }

    public function getFoyer(): ?Foyer
    {
        return $this->foyer;
    }

    public function setFoyer(?Foyer $foyer): self
    {
        $this->foyer = $foyer;

        return $this;
    }

    // Méthode pour calculer le prix par trimestre
    public function calculerPrixTrimestre(): int
    {
        // Prix de base selon le type de chambre
        $prixBase = $this->type === 'double' ? 450 : 750;

        // Ajouter le coût du climatiseur
        if ($this->climatiseur) {
            $prixBase += 200;
        }

        // Ajouter le coût du chauffage central
        if ($this->chauffageCentral) {
            $prixBase += 100;
        }

        return $prixBase;
    }

    public function getPlacesDisponibles(): ?int
    {
        return $this->PlacesDisponibles;
    }

    public function setPlacesDisponibles(int $PlacesDisponibles): static
    {
        $this->PlacesDisponibles = $PlacesDisponibles;

        return $this;
    }

    public function __toString(): string
    {
        // Customize this to return a meaningful string representation
        return $this->getNumeroChambre(); // Assuming there is a getId() method
    }
}
