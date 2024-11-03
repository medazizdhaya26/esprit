<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $responsable_nom = null;

    #[ORM\Column]
    private ?int $responsable_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponsableNom(): ?string
    {
        return $this->responsable_nom;
    }

    public function setResponsableNom(string $responsable_nom): static
    {
        $this->responsable_nom = $responsable_nom;

        return $this;
    }

    public function getResponsableId(): ?int
    {
        return $this->responsable_id;
    }

    public function setResponsableId(int $responsable_id): static
    {
        $this->responsable_id = $responsable_id;

        return $this;
    }
}
