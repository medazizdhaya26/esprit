<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Bibliotheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null; 

    #[ORM\Column(length: 30)]
    private ?int $idresponsable = null;

    #[ORM\OneToMany(targetEntity: Livre::class, mappedBy: 'bibliotheque', cascade: ['persist', 'remove'])]
    private Collection $livres;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
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

    public function getidresponsable(): ?string
    {
        return $this->idresponsable;
    }

    public function setidresponsable(string $idresponsable): static
    {
        $this->idresponsable = $idresponsable;
        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): static
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setBibliotheque($this);
        }
        return $this;
    }

    public function removeLivre(Livre $livre): static
    {
        if ($this->livres->removeElement($livre)) {
            if ($livre->getBibliotheque() === $this) {
                $livre->setBibliotheque(null);
            }
        }
        return $this;
    }
}
