<?php

namespace App\Entity;

use App\Repository\ProfessionalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Config\UserRole;
use App\Config\ProfessionalPosition;

/**
 * Représente un professionnel de l'insertion dans le système
 */
#[ORM\Entity(repositoryClass: ProfessionalRepository::class)]
class Professional extends User
{
    #[ORM\Column(length: 255)]
    private ?string $organisation = null;

    #[ORM\Column(type: 'string', enumType: ProfessionalPosition::class)]
    private ProfessionalPosition $position;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'createdBy')]
    private Collection $events;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles([UserRole::ROLE_PROFESSIONAL->value]);
        $this->position = ProfessionalPosition::NON_DEFINI;
        $this->events = new ArrayCollection();
    }

    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    public function setOrganisation(string $organisation): static
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getPosition(): ProfessionalPosition
    {
        return $this->position;
    }

    public function setPosition(ProfessionalPosition $position): static
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCreatedBy($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCreatedBy() === $this) {
                $event->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
}
