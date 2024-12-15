<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'paniers')]
    #[ORM\JoinTable(name: 'panier_produit')]
    private Collection $produits;

    #[ORM\Column(type: 'float')]
    private ?float $prixTotal = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'panier', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: TestPanierProduit::class, mappedBy: 'panier', cascade: ['persist'])]
    private Collection $testPanierProduits;
    
    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->prixTotal = 0;
        $this->testPanierProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addPanier($this);

            if ($produit->getPrix() !== null) {
                $this->prixTotal += $produit->getPrix();
            }
        }
        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removePanier($this);

            if ($produit->getPrix() !== null) {
                $this->prixTotal -= $produit->getPrix();
            }
        }
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Méthodes pour gérer les produits dans le panier via TestPanierProduit

    public function getTestPanierProduits(): Collection
    {
        return $this->testPanierProduits;
    }

    public function addTestPanierProduit(TestPanierProduit $testPanierProduits): self
    {
        if (!$this->testPanierProduits->contains($testPanierProduits)) {
            $this->testPanierProduits[] = $testPanierProduits;
            $testPanierProduits->setPanier($this);
        }

        return $this;
    }

    public function removeTestPanierProduit(TestPanierProduit $testPanierProduits): self
    {
        $this->testPanierProduits->removeElement($testPanierProduits);
        return $this;
    }

    // Méthode pour calculer le prix total du panier
    public function getPrixTotal(): ?float
    {
        $total = 0;
        foreach ($this->testPanierProduits as $testPanierProduits) {
            $total += $testPanierProduits->getProduit()->getPrix() * $testPanierProduits->getQuantite();
        }
        return $total;
    }

    public function setPrixTotal(float $prixTotal): self
    {
        $this->prixTotal = $prixTotal;
        return $this;
    }

    // Méthode pour obtenir la quantité d'un produit spécifique dans le panier
    public function getQuantiteProduit(Produit $produit): int
    {
        $quantite = 0;

        // Parcourir les produits dans le panier et compter combien de fois le produit est présent
        foreach ($this->testPanierProduits as $testPanierProduits) {
            if ($testPanierProduits->getProduit() === $produit) {
                $quantite += $testPanierProduits->getQuantite();
            }
        }

        // Retourner la quantité du produit dans le panier
        return $quantite;
    }
}
