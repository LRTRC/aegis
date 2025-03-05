<?php

namespace App\Controller\Admin;

use App\Config\ParticipantStatus;
use App\Entity\Participant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Participant')
            ->setEntityLabelInPlural('Participants')
            ->setPageTitle('index', 'Liste des participants')
            ->setPageTitle('new', 'Ajouter un participant')
            ->setPageTitle('edit', function (Participant $participant) {
                return sprintf(
                    'Modifier %s %s - %s',
                    $participant->getFirstName(),
                    $participant->getLastName(),
                    $participant->getNumeroFT() ?? 'Sans numéro FT'
                );
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email')
                ->setRequired(true)
                ->setHelp('Adresse email du participant'),

            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->setHelp('Laissez vide pour conserver le mot de passe actuel'),

            TextField::new('firstName', 'Prénom')
                ->setRequired(true),

            TextField::new('lastName', 'Nom')
                ->setRequired(true),

            TextField::new('phoneNumber', 'Téléphone')
                ->setRequired(false)
                ->setHelp('Format : 06 12 34 56 78'),

            TextField::new('numeroFT', 'Numéro France Travail')
                ->setRequired(false)
                ->setHelp('Numéro d\'identifiant France Travail'),

            ChoiceField::new('statutProfessionnel', 'Statut')
                ->setChoices([
                    'En Recherche' => ParticipantStatus::EN_RECHERCHE,
                    'En Formation' => ParticipantStatus::EN_FORMATION,
                    'En Stage' => ParticipantStatus::EN_STAGE,
                    'En Emploi' => ParticipantStatus::EN_EMPLOI,
                    'Non Défini' => ParticipantStatus::NON_DEFINI,
                ])
                ->renderAsBadges([
                    ParticipantStatus::EN_RECHERCHE->value => 'warning',
                    ParticipantStatus::EN_FORMATION->value => 'info',
                    ParticipantStatus::EN_STAGE->value => 'primary',
                    ParticipantStatus::EN_EMPLOI->value => 'success',
                    ParticipantStatus::NON_DEFINI->value => 'dark',
                ]),

            CollectionField::new('competences', 'Compétences')
                ->onlyOnForms()
                ->setHelp('Liste des compétences du participant'),

            AssociationField::new('sessions', 'Sessions')
                ->onlyOnDetail()
                ->setHelp('Sessions auxquelles le participant est inscrit'),

            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd/MM/Y HH:mm'),

            DateTimeField::new('lastLogin', 'Dernière connexion')
                ->hideOnForm()
                ->setFormat('dd/MM/Y HH:mm'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPassword($participant): void
    {
        if (!$participant instanceof Participant) {
            return;
        }

        if ($password = $participant->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $participant,
                $password
            );
            $participant->setPassword($hashedPassword);
        }
    }
}