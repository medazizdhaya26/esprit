<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SalleDeSport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column()]
    private ?int $id_univercity = null;

    #[ORM\Column(length: 255)]
    private ?string $coechname = null;

    #[ORM\Column(length: 255)]
    private ?int $id_superviseur = null;

    #[ORM\Column(length: 255)]
    private ?string $prix_des_abonnement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUnivercite(): ?string
    {
        return $this->id_univercity;
    }

    public function setIdUnivercite(string $id_univercity): static
    {
        $this->id_univercity = $id_univercity;
        return $this;
    }

    public function getcoechname(): ?string
    {
        return $this->coechname;
    }

    public function setcoechname(string $coechname): static
    {
        $this->coechname = $coechname;
        return $this;
    }

    public function getid_superviseur(): ?string
    {
        return $this->id_superviseur;
    }

    public function setid_superviseur(string $id_superviseur): static
    {
        $this->id_superviseur = $id_superviseur;
        return $this;
    }

    public function getPrixDesAbonnement(): ?string
    {
        return $this->prix_des_abonnement;
    }

    public function setPrixDesAbonnement(string $prix_des_abonnement): static
    {
        $this->prix_des_abonnement = $prix_des_abonnement;
        return $this;
    }
}
