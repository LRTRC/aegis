<?php

namespace App\Controller\Admin;

use App\Config\ProfessionalPosition;
use App\Entity\Professional;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfessionalCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return Professional::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Professionnel')
            ->setEntityLabelInPlural('Professionnels')
            ->setPageTitle('index', 'Liste des professionnels')
            ->setPageTitle('new', 'Ajouter un professionnel')
            ->setPageTitle('edit', function (Professional $professional) {
                return sprintf(
                    'Modifier %s %s - %s',
                    $professional->getFirstName(),
                    $professional->getLastName(),
                    $professional->getOrganisation()
                );
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email')
                ->setRequired(true)
                ->setHelp('Adresse email professionnelle'),

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

            TextField::new('organisation', 'Organisation')
                ->setRequired(true)
                ->setHelp('Nom de l\'organisation'),

            ChoiceField::new('position', 'Poste')
                ->setChoices([
                    'Conseiller' => ProfessionalPosition::CONSEILLER,
                    'Formateur' => ProfessionalPosition::FORMATEUR,
                    'Coordinateur' => ProfessionalPosition::COORDINATEUR,
                    'Responsable' => ProfessionalPosition::RESPONSABLE,
                    'Non Défini' => ProfessionalPosition::NON_DEFINI,
                ])
                ->renderAsBadges([
                    ProfessionalPosition::CONSEILLER->value => 'primary',
                    ProfessionalPosition::FORMATEUR->value => 'success',
                    ProfessionalPosition::COORDINATEUR->value => 'warning',
                    ProfessionalPosition::RESPONSABLE->value => 'danger',
                    ProfessionalPosition::NON_DEFINI->value => 'dark',
                ]),

            AssociationField::new('events', 'Événements')
                ->onlyOnDetail()
                ->setHelp('Liste des événements créés'),

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

    private function hashPassword($professional): void
    {
        if (!$professional instanceof Professional) {
            return;
        }

        if ($password = $professional->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $professional,
                $password
            );
            $professional->setPassword($hashedPassword);
        }
    }
}