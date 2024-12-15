<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Bibliotheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser 255 caractères.')]
    private ?string $nom = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message: 'La capacité est obligatoire.')]
    #[Assert\Positive(message: 'La capacité doit être un nombre positif.')]
    private ?int $capacite = null;

    
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Les horaires d\'ouverture sont obligatoires.')]
    private ?string $horairesOuverture = null;  

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le contact administratif est obligatoire.')]
    private ?string $contactAdministratif = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'L\'adresse ne peut pas dépasser 255 caractères.')]
    private ?string $adresse = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'La description ne peut pas dépasser 1000 caractères.')]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'bibliotheque', targetEntity: Bibliothecaire::class, cascade: ['persist', 'remove'])]
    private Collection $bibliothecaires;

    public function __construct()
    {
        $this->bibliothecaires = new ArrayCollection();
    }

    // Getters and Setters

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

    public function getHorairesOuverture(): ?string
    {
        return $this->horairesOuverture;
    }

    public function setHorairesOuverture($horairesOuverture): self
{
    // Si c'est un array, on le convertit en string
    if (is_array($horairesOuverture)) {
        $this->horairesOuverture = implode(', ', $horairesOuverture);
    } else {
        $this->horairesOuverture = $horairesOuverture;
    }
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection|Bibliothecaire[]
     */
    public function getBibliothecaires(): Collection
    {
        return $this->bibliothecaires;
    }

    public function addBibliothecaire(Bibliothecaire $bibliothecaire): self
    {
        if (!$this->bibliothecaires->contains($bibliothecaire)) {
            $this->bibliothecaires[] = $bibliothecaire;
            $bibliothecaire->setBibliotheque($this);
        }

        return $this;
    }

    public function removeBibliothecaire(Bibliothecaire $bibliothecaire): self
{
    if ($this->bibliothecaires->contains($bibliothecaire)) {
        $this->bibliothecaires->removeElement($bibliothecaire);
        $bibliothecaire->setBibliotheque(null); // Si la relation est bidirectionnelle
    }

    return $this;
}

    
}
