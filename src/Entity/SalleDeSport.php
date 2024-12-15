<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SalleDeSport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;


    
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le nom de la salle ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom de la salle doit contenir au moins {{ limit }} caractères.")]
    private ?string $nom = null;


    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "La capacité de la salle ne peut pas être vide.")]
    #[Assert\GreaterThan(value: 0, message: "La capacité doit être supérieure à zéro.")]
    private ?int $capacite = null;



    #[ORM\Column(type: 'json')]
    private array $equipements = [];


 #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Les horaires d'ouverture ne peuvent pas être vides.")]
    
    private ?string $horairesOuverture = null;


    #[Assert\NotBlank(message: "Le contact administratif ne peut pas être vide.")]
    #[Assert\Email(message: "Le contact administratif doit être un email valide.")]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $contactAdministratif = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Entraineur::class, cascade: ['persist', 'remove'])]
    private Collection $entraineurs;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    // Relation OneToMany avec l'entité Avis
    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Avis::class, cascade: ['persist', 'remove'])]
    private Collection $avis;

    public function __construct()
    {
        $this->entraineurs = new ArrayCollection();
        $this->avis = new ArrayCollection();  // Initialisation de la collection d'avis
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    public function getEquipements(): array
    {
        return $this->equipements;
    }

    public function setEquipements(array $equipements): self
    {
        $this->equipements = $equipements;
        return $this;
    }

    public function getHorairesOuverture(): ?string
    {
        return $this->horairesOuverture;
    }

    public function setHorairesOuverture(string $horairesOuverture): self
    {
        $this->horairesOuverture = $horairesOuverture;
        return $this;
    }

    public function getContactAdministratif(): ?string
    {
        return $this->contactAdministratif;
    }

    public function setContactAdministratif(string $contactAdministratif): self
    {
        $this->contactAdministratif = $contactAdministratif;
        return $this;
    }

    public function getEntraineurs(): Collection
    {
        return $this->entraineurs;
    }

    public function addEntraineur(Entraineur $entraineur): self
    {
        if (!$this->entraineurs->contains($entraineur)) {
            $this->entraineurs[] = $entraineur;
            $entraineur->setSalle($this);
        }
        return $this;
    }

    public function removeEntraineur(Entraineur $entraineur): self
    {
        if ($this->entraineurs->removeElement($entraineur)) {
            if ($entraineur->getSalle() === $this) {
                $entraineur->setSalle(null);
            }
        }
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    // Méthode d'accès aux avis
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setSalle($this);
        }
        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->avis->removeElement($avis)) {
            if ($avis->getSalle() === $this) {
                $avis->setSalle(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        // Retournez une chaîne de caractères représentative, comme le nom de la salle
        return $this->nom ?? 'Salle de sport';
    }
}
