<?php

namespace App\Entity;

use App\Repository\CeluleEcouteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CeluleEcouteRepository::class)]
class CeluleEcoute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $num_consultation = null;

    #[ORM\Column(length: 30)]
    private ?string $nom_medcin = null;

    #[ORM\Column(length: 30)]
    private ?string $nom_patient = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom_patient = null;

    public function getNumConsultation(): ?int
    {
        return $this->id;
    }

    public function getNomMedcin(): ?string
    {
        return $this->nom_medcin;
    }

    public function setNomMedcin(string $nom_medcin): static
    {
        $this->nom_medcin = $nom_medcin;

        return $this;
    }

    public function getNomPatient(): ?string
    {
        return $this->nom_patient;
    }

    public function setNomPatient(string $nom_patient): static
    {
        $this->nom_patient = $nom_patient;

        return $this;
    }

    public function getPrenomPatient(): ?string
    {
        return $this->prenom_patient;
    }

    public function setPrenomPatient(string $prenom_patient): static
    {
        $this->prenom_patient = $prenom_patient;

        return $this;
    }
}
