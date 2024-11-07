<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Enseignant extends User
{

    #[ORM\Column(length: 20)]
    private ?string $matiere = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $salaire = null;



    #[ORM\ManyToMany(targetEntity: Club::class, inversedBy: 'superviseurs')]
    private Collection $clubs;

    #[ORM\ManyToOne(targetEntity: Bibliotheque::class)]
    private ?Bibliotheque $bibliotheque = null;

    #[ORM\ManyToOne(targetEntity: SalleDeSport::class)]
    private ?SalleDeSport $salleDeSport = null;

    #[ORM\ManyToOne(targetEntity: Restaurant::class)]
    private ?Restaurant $restaurant = null;

    public function __construct()
    {
        $this->clubs = new ArrayCollection();
    }



    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): static
    {
        $this->matiere = $matiere;
        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): static
    {
        $this->salaire = $salaire;
        return $this;
    }

    // New getter and setter for role


    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): static
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs[] = $club;
        }
        return $this;
    }

    public function removeClub(Club $club): static
    {
        $this->clubs->removeElement($club);
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

    public function getSalleDeSport(): ?SalleDeSport
    {
        return $this->salleDeSport;
    }

    public function setSalleDeSport(?SalleDeSport $salleDeSport): static
    {
        $this->salleDeSport = $salleDeSport;
        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;
        return $this;
    }
}
