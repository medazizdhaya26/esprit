<?php

namespace App\Entity;

use App\Repository\FoyerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FoyerRepository::class)]
class Foyer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom du foyer est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s]+$/",
        message: "Le nom du foyer doit contenir uniquement des lettres et des espaces."
    )]
    private ?string $nomFoyer = null;

    #[ORM\Column(nullable:true)]
    //#[Assert\NotBlank(message: "Le nombre de chambres est obligatoire.")]
   // #[Assert\PositiveOrZero(message: "Le nombre de chambres ne peut pas être négatif.")]
    private ?int $nombreChambre = null;

    #[ORM\Column]
    private ?int $nombreEtage = null;

    #[ORM\Column(length: 100)]
    private ?string $lieuFoyer = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le nombre de chambres simples ne peut pas être vide.")]
    #[Assert\Type(type:"integer", message:"Le nombre de chambres simples doit être un nombre entier.")]
    #[Assert\GreaterThanOrEqual(value:0, message:"Le nombre de chambres simples ne peut pas être négatif.")]

    private ?int $nombreChambresSingle = 0;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le nombre de chambres doubles ne peut pas être vide.")]
    #[Assert\PositiveOrZero(message:"Le nombre de chambres doubles doit être positif ou zéro.")]
    private ?int $nombreChambresDouble = 0;

    #[ORM\OneToMany(mappedBy: 'foyer', targetEntity: Chambre::class, cascade: ['persist', 'remove'])]
    private Collection $chambres;

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFoyer(): ?string
    {
        return $this->nomFoyer;
    }

    public function setNomFoyer(string $nomFoyer): self
    {
        $this->nomFoyer = $nomFoyer;

        return $this;
    }

    public function getNombreChambre(): ?int
    {
        return $this->nombreChambre;
    }

    public function setNombreChambre(int $nombreChambre): self
    {
        $this->nombreChambre = $nombreChambre;

        return $this;
    }

    public function getNombreEtage(): ?int
    {
        return $this->nombreEtage;
    }

    public function setNombreEtage(int $nombreEtage): self
    {
        $this->nombreEtage = $nombreEtage;

        return $this;
    }

    public function getLieuFoyer(): ?string
    {
        return $this->lieuFoyer;
    }

    public function setLieuFoyer(string $lieuFoyer): self
    {
        $this->lieuFoyer = $lieuFoyer;

        return $this;
    }

    public function getNombreChambresSingle(): ?int
    {
        return $this->nombreChambresSingle;
    }

    public function setNombreChambresSingle(int $nombreChambresSingle): self
    {
        $this->nombreChambresSingle = $nombreChambresSingle;

        return $this;
    }

    public function getNombreChambresDouble(): ?int
    {
        return $this->nombreChambresDouble;
    }

    public function setNombreChambresDouble(int $nombreChambresDouble): self
    {
        $this->nombreChambresDouble = $nombreChambresDouble;

        return $this;
    }

    /**
     * @return Collection<int, Chambre>
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): self
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres->add($chambre);
            $chambre->setFoyer($this);

            // Mise à jour du comptage en fonction du type
            if ($chambre->getType() === 'single') {
                $this->nombreChambresSingle++;
            } elseif ($chambre->getType() === 'double') {
                $this->nombreChambresDouble++;
            }
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): self
    {
        if ($this->chambres->removeElement($chambre)) {
            if ($chambre->getFoyer() === $this) {
                $chambre->setFoyer(null);

                // Mise à jour du comptage en fonction du type
                if ($chambre->getType() === 'single') {
                    $this->nombreChambresSingle--;
                } elseif ($chambre->getType() === 'double') {
                    $this->nombreChambresDouble--;
                }
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // Customize this to return a meaningful string representation
        return $this->getId(); // Assuming there is a getId() method
    }
}
