<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Etudiant extends User
{

    #[ORM\Column(length: 20)]
    private ?string $filiere = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $payement = null;


    #[ORM\ManyToOne(targetEntity: Bibliotheque::class)]
    private ?Bibliotheque $bibliotheque = null;

    #[ORM\ManyToOne(targetEntity: SalleDeSport::class)]
    private ?SalleDeSport $salleDeSport = null;

    #[ORM\ManyToOne(targetEntity: Restaurant::class)]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToMany(targetEntity: Club::class, inversedBy: 'etudiants')]
    private Collection $clubs;

    public function __construct()
    {
        $this->clubs = new ArrayCollection();
    }


    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(string $filiere): static
    {
        $this->filiere = $filiere;
        return $this;
    }

    public function getPayement(): ?bool
    {
        return $this->payement;
    }

    public function setPayement(?bool $payement): static
    {
        $this->payement = $payement;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    // Setter method that allows setting the role to the constant only
    public function setRole(): static
    {
        $this->role = self::ROLE_SIMPLE_USER;
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

    public function isValidate(): ?bool
    {
        return $this->is_validate;
    }

    public function setValidate(bool $is_validate): static
    {
        $this->is_validate = $is_validate;

        return $this;
    }
}
