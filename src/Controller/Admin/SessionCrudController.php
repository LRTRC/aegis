<?php

namespace App\Controller\Admin;

use App\Config\EventStatus;
use App\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Session')
            ->setEntityLabelInPlural('Sessions')
            ->setPageTitle('index', 'Liste des sessions')
            ->setPageTitle('new', 'Créer une session')
            ->setPageTitle('edit', function (Session $session) {
                return sprintf(
                    'Modifier la session du %s (%s)',
                    $session->getDateDebut()->format('d/m/Y'),
                    $session->getEvent()->getTitre()
                );
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('event', 'Événement')
                ->setRequired(true)
                ->setHelp('Événement auquel cette session est rattachée'),

            DateTimeField::new('dateDebut', 'Date de début')
                ->setFormat('dd/MM/Y HH:mm')
                ->setRequired(true)
                ->setHelp('Date et heure de début de la session'),

            DateTimeField::new('dateFin', 'Date de fin')
                ->setFormat('dd/MM/Y HH:mm')
                ->setRequired(true)
                ->setHelp('Date et heure de fin de la session'),

            TextField::new('lieu', 'Lieu')
                ->setRequired(false)
                ->setHelp('Si différent du lieu de l\'événement'),

            IntegerField::new('capaciteMax', 'Capacité maximale')
                ->setRequired(true)
                ->setHelp('Nombre maximum de participants pour cette session'),

            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Brouillon' => EventStatus::DRAFT,
                    'Publié' => EventStatus::PUBLISHED,
                    'Annulé' => EventStatus::CANCELLED,
                    'Terminé' => EventStatus::COMPLETED,
                ])
                ->renderAsBadges([
                    EventStatus::DRAFT->value => 'secondary',
                    EventStatus::PUBLISHED->value => 'success',
                    EventStatus::CANCELLED->value => 'danger',
                    EventStatus::COMPLETED->value => 'info',
                ]),

            AssociationField::new('participants', 'Participants')
                ->onlyOnDetail()
                ->setHelp('Liste des participants inscrits à cette session'),

            DateTimeField::new('createdAt', 'Créé le')
                ->hideOnForm()
                ->setFormat('dd/MM/Y HH:mm'),

            DateTimeField::new('updatedAt', 'Modifié le')
                ->onlyOnDetail()
                ->setFormat('dd/MM/Y HH:mm'),
        ];
    }
}
