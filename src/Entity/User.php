<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Config\UserRole;

/**
 * Classe abstraite représentant un utilisateur de base dans le système
 * Implémente UserInterface pour l'intégration avec le système de sécurité Symfony
 *
 * @author Thomas Loir
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "participant" => Participant::class,
    "professional" => Professional::class
])]
#[ORM\HasLifecycleCallbacks]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Email de l'utilisateur, utilisé comme identifiant unique de connexion
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * Rôles de l'utilisateur dans le système
     * @var array<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé de l'utilisateur
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Prénom de l'utilisateur
     */
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    /**
     * Nom de l'utilisateur
     */
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * Numéro de téléphone de l'utilisateur
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    /**
     * Date de création du compte
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Date de dernière modification du compte
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Date de dernière connexion
     */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLogin = null;

    /**
     * Statut du compte (actif/inactif)
     */
    #[ORM\Column]
    private bool $isActive = true;

    /**
     * Date de naissance de l'utilisateur
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->roles = [];
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isActive = true;
    }

    /**
     * Initialisation des timestamps à la création
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Mise à jour du timestamp de modification
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Obtient l'identifiant de l'utilisateur
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtient l'email de l'utilisateur
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'email de l'utilisateur
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Obtient les rôles de l'utilisateur
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantit que chaque utilisateur a au moins ROLE_USER
        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur
     * @param array<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Obtient le prénom de l'utilisateur
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): static
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Dans le cadre de données sensibles stocker provisoirement, à effacer ici
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }
}
