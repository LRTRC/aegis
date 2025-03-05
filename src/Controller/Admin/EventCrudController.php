<?php

namespace App\Controller\Admin;

use App\Config\EventType;
use App\Config\EventStatus;
use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Événement')
            ->setEntityLabelInPlural('Événements')
            ->setPageTitle('index', 'Liste des événements')
            ->setPageTitle('new', 'Créer un événement')
            ->setPageTitle('edit', function (Event $event) {
                return 'Modifier l\'événement : ' . $event->getTitre();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre', 'Titre')
                ->setRequired(true)
                ->setHelp('Titre de l\'événement'),

            TextEditorField::new('description')
                ->setRequired(false)
                ->hideOnIndex(),

            DateTimeField::new('dateDebut', 'Date de début')
                ->setFormat('dd/MM/Y HH:mm')
                ->setRequired(false),

            DateTimeField::new('dateFin', 'Date de fin')
                ->setFormat('dd/MM/Y HH:mm')
                ->setRequired(false),

            TextField::new('lieu', 'Lieu')
                ->setRequired(false),

            IntegerField::new('capaciteMax', 'Capacité maximale')
                ->setRequired(true)
                ->setHelp('Nombre maximum de participants'),

            ChoiceField::new('type', 'Type d\'événement')
                ->setChoices([
                    'Job Dating' => EventType::JOB_DATING,
                    'Formation' => EventType::FORMATION,
                    'Atelier' => EventType::ATELIER,
                    'Réunion' => EventType::REUNION,
                    'Non défini' => EventType::NON_DEFINI,
                ])
                ->renderAsBadges([
                    EventType::JOB_DATING->value => 'success',
                    EventType::FORMATION->value => 'primary',
                    EventType::ATELIER->value => 'info',
                    EventType::REUNION->value => 'warning',
                    EventType::NON_DEFINI->value => 'dark',
                ]),

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

            AssociationField::new('createdBy', 'Créé par')
                ->setRequired(true)
                ->formatValue(function ($value, $entity) {
                    return $value ? $value : 'Non assigné';
                }),

            AssociationField::new('sessions', 'Sessions')
                ->onlyOnDetail(),
        ];
    }
}
