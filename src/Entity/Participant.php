<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Config\ParticipantStatus;

/**
 * Représente un demandeur d'emploi dans le système
 */
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant extends User
{
    /**
     * Numéro France Travail du participant
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroFT = null;

    /**
     * Statut professionnel actuel
     */
    #[ORM\Column(type: 'string', enumType: ParticipantStatus::class)]
    private ParticipantStatus $statutProfessionnel;

    /**
     * Compétences du participant
     */
    #[ORM\Column]
    private array $competences = [];

    /**
     * @var Collection<int, Session>
     */
    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'participants')]
    private Collection $sessions;

    public function __construct()
    {
        parent::__construct();  // Appel du constructeur parent
        $this->statutProfessionnel = ParticipantStatus::NON_DEFINI;  // Valeur par défaut
        $this->competences = [];  // Initialisation du tableau des compétences
        $this->sessions = new ArrayCollection();
    }

    public function getNumeroFT(): ?string
    {
        return $this->numeroFT;
    }

    public function setNumeroFT(?string $numeroFT): static
    {
        $this->numeroFT = $numeroFT;
        return $this;
    }

    public function getStatutProfessionnel(): ParticipantStatus
    {
        return $this->statutProfessionnel;
    }

    public function setStatutProfessionnel(ParticipantStatus $statutProfessionnel): static
    {
        $this->statutProfessionnel = $statutProfessionnel;
        return $this;
    }

    public function getCompetences(): array
    {
        return $this->competences;
    }

    public function setCompetences(array $competences): static
    {
        $this->competences = $competences;
        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addParticipant($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            $session->removeParticipant($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
}
