<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $type = null;

    #[ORM\Column(length: 30)]
    private ?string $president = null;

    #[ORM\Column(length: 30)]
    private ?string $supervisor = null;

    #[ORM\ManyToMany(targetEntity: Etudiant::class, mappedBy: 'clubs')]
    private Collection $etudiants;

    #[ORM\ManyToMany(targetEntity: Enseignant::class, mappedBy: 'clubs')]
    private Collection $superviseurs;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->superviseurs = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getPresident(): ?string
    {
        return $this->president;
    }

    public function setPresident(string $president): static
    {
        $this->president = $president;
        return $this;
    }

    public function getSupervisor(): ?string
    {
        return $this->supervisor;
    }

    public function setSupervisor(string $supervisor): static
    {
        $this->supervisor = $supervisor;
        return $this;
    }

    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
        }
        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        $this->etudiants->removeElement($etudiant);
        return $this;
    }

    public function getSuperviseurs(): Collection
    {
        return $this->superviseurs;
    }

    public function addSuperviseur(Enseignant $superviseur): static
    {
        if (!$this->superviseurs->contains($superviseur)) {
            $this->superviseurs[] = $superviseur;
        }
        return $this;
    }

    public function removeSuperviseur(Enseignant $superviseur): static
    {
        $this->superviseurs->removeElement($superviseur);
        return $this;
    }
}
