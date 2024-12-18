<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use App\Repository\BiblioAbonementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BiblioAbonementRepository::class)]
class BiblioAbonement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255)]

    #[ORM\JoinColumn(nullable: false)]
    private ?int $id_bibliotheque = null;

    public function getIdBibliotheque(): ?int
    {
        return $this->id_bibliotheque;
    }

    public function setIdBibliotheque(?int $id_bibliotheque): void
    {
        $this->id_bibliotheque = $id_bibliotheque;
    }

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Email(message: 'Veuillez fournir un email valide.')]
    private ?string $email = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: 'dateDebut', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeInterface $dateFin = null;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBibliotheque(): ?Bibliotheque
    {
        return $this->bibliotheque;
    }

    public function setBibliotheque(?Bibliotheque $bibliotheque): self
    {
        $this->bibliotheque = $bibliotheque;

        return $this;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }
}
