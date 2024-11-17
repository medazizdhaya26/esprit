<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $responsable_email = null;

    #[ORM\Column]
    private ?int $responsable_id = null;

    #[ORM\Column(length: 100)]
    private ?string $titreevets = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 50)]
    private ?string $lieu = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $nombre_participants_max = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $valider = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $refuser = false;

    #[ORM\Column(length: 50)]
    private ?string $type_events = null;

    #[ORM\OneToMany(mappedBy: "evenement", targetEntity: Reservation::class, cascade: ["persist", "remove"])]
    private Collection $reservations;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponsableEmail(): ?string
    {
        return $this->responsable_email;
    }

    public function setResponsableEmail(?string $responsable_email): void
    {
        $this->responsable_email = $responsable_email;
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

    public function getTitreevets(): ?string
    {
        return $this->titreevets;
    }

    public function setTitreevets(string $titreevets): static
    {
        $this->titreevets = $titreevets;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getNombreParticipantsMax(): ?int
    {
        return $this->nombre_participants_max;
    }

    public function setNombreParticipantsMax(?int $nombre_participants_max): static
    {
        $this->nombre_participants_max = $nombre_participants_max;
        return $this;
    }

    public function getValider(): bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): static
    {
        $this->valider = $valider;
        return $this;
    }

    public function getTypeEvents(): ?string
    {
        return $this->type_events;
    }

    public function setTypeEvents(string $type_events): static
    {
        $this->type_events = $type_events;
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvenement($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getEvenement() === $this) {
                $reservation->setEvenement(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function isRefuser(): bool
    {
        return $this->refuser;
    }

    public function setRefuser(bool $refuser): void
    {
        $this->refuser = $refuser;
    }
}
