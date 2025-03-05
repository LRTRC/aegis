<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Config\EventStatus;
use App\Config\EventType;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $capaciteMax = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professional $createdBy = null;

    #[ORM\Column(type: 'string', enumType: EventType::class)]
    private EventType $type;

    #[ORM\Column(type: 'string', enumType: EventStatus::class)]
    private EventStatus $status;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'event')]
    private Collection $sessions;

    public function __construct()
    {
        $this->type = EventType::NON_DEFINI;
        $this->status = EventStatus::DRAFT;
        $this->sessions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapaciteMax(): ?int
    {
        return $this->capaciteMax;
    }

    public function setCapaciteMax(int $capaciteMax): static
    {
        $this->capaciteMax = $capaciteMax;

        return $this;
    }

    public function getCreatedBy(): ?Professional
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Professional $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getType(): EventType
    {
        return $this->type;
    }

    public function setType(EventType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    /**
     * Summary of addSession
     * @param \App\Entity\Session $session
     * @return Event
     */
    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setEvent($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getEvent() === $this) {
                $session->setEvent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return "{$this->getTitre()}";
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            EventStatus::DRAFT => 'Brouillon',
            EventStatus::PUBLISHED => 'Publié',
            EventStatus::CANCELLED => 'Annulé',
            EventStatus::COMPLETED => 'Terminé',
        };
    }

    public function getTypeLabel(?EventType $typeValue = null): string
    {
        // Si typeValue est null, on utilise le type de l'instance courante
        $type = $typeValue ?? $this->type;

        return match ($type) {
            EventType::JOB_DATING => 'Job Dating',
            EventType::FORMATION => 'Formation',
            EventType::ATELIER => 'Atelier',
            EventType::REUNION => 'Réunion',
            EventType::NON_DEFINI => 'Non défini',
        };
    }
}
