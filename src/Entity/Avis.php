<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    #[Assert\Range(min: 1, max: 5)]
    private $note;

    #[ORM\Column(type: "text")]
    private $commentaire;

    #[ORM\ManyToOne(targetEntity: SalleDeSport::class, inversedBy: "avis")]
    private $salle;

    #[ORM\ManyToOne(targetEntity: Entraineur::class, inversedBy: "avis")]
    private $entraineur;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $utilisateur;

    // Ajout du champ pseudo
    #[ORM\Column(type: "string")]
    private $pseudo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getSalle(): ?SalleDeSport
    {
        return $this->salle;
    }

    public function setSalle(?SalleDeSport $salle): self
    {
        $this->salle = $salle;
        return $this;
    }

    public function getEntraineur(): ?Entraineur
    {
        return $this->entraineur;
    }

    public function setEntraineur(?Entraineur $entraineur): self
    {
        $this->entraineur = $entraineur;
        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    // Getter et Setter pour le pseudo
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;
        return $this;
    }
}
