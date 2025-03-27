<?php

namespace App\Controller\Admin; // Définit le nom pour ce contrôleur dans le répertoire Admin

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // Importe la classe Filters pour configurer les filtres dans la liste des notifications
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters; // Importe la classe Filters pour configurer les filtres dans la liste de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter; // Importe le filtre ChoiceFilter pour ajouter un filtre de type liste déroulante dans l'interface
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter; // Importe le filtre DateTimeFilter pour ajouter un filtre de plage de dates dans l'interface
use App\Entity\Notification; // Importe l'entité Notification
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Importe le champ pour le texte
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions; // Importe la configuration des actions
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; // Importe la configuration CRUD d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Classe de base pour les contrôleurs CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importe le champ IdField pour afficher et gérer l'identifiant de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Importe le champ DateTimeField pour afficher et éditer des dates/heures
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Importe le champ AssociationField pour gérer les relations entre entités (Notification -> User)

class NotificationController extends AbstractCrudController
{
    public static function getEntityFqcn(): String // Méthode statique qui retourne le nom de l'entité gérée
    {
        return Notification::class; // Indique que contrôleur gère l'entité Notification
    }

    public function configureFields(string $notification): iterable // Configure les champs qui seront affichés dans l'interface d'administration
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire mais visible dans la liste
            TextField::new('title', 'Titre'), // Champ texte pour le titre avec l'étiquette "titre"
            TextField::new('content', 'Contenu'), // Champ texte pour le contenu avec l'étiquette "contenu"
            ChoiceField::new('channel', 'Canal')->setChoices([ // Champ de canal avec une liste déroulante pour email, SMS ou push
                'Email' => 'Email',
                'SMS' => 'Sms',
                'Push' => 'Push',
            ]),
            DateTimeField::new('date', 'Date de creation'), // Champ date/heure pour la date de création
            AssociationField::new('Users', 'Utilisateur'),  // Champ de relation vers l'entité Users (relation ManyToOne)
        ];
    }

    public function configureCrud(Crud $crud): Crud // Configure les options globales du CRUD
    {
    return $crud
        ->setEntityLabelInSingular('Notification') // Label au singulier pour une notification
        ->setEntityLabelInPlural('Notifications') // Label au pluriel pour les notifications
        ->setSearchFields(['title', 'content']) // Champs de recherche avec les titles (tri)
        ->setDefaultSort(['name' => 'DESC']) // Tri par date décroissante
    ;
    }

    public function configureActions(Actions $actions): Actions // Configure les actions disponibles dans l'interface
    {
    return $actions
        ->add(Crud::PAGE_INDEX, 'detail') // Ajoute une action "détail" sur la page d'index (Notification)
        ->add(Crud::PAGE_EDIT, 'detail') // Ajoute une action "détail" sur la page d'édition
    ;
    }

    public function configureFilters(Filters $filters): Filters // Configure les filtres disponibles dans la liste des notifications
    {
        return $filters
            ->add(ChoiceFilter::new('channel', 'Canal')->setChoices([ // Filtre sur le canal avec une liste déroulante des mêmes choix que ChoiceField
                'Email' => 'email',
                'SMS' => 'sms',
                'Push' => 'push',
            ]))
            ->add(DateTimeFilter::new('date', 'Date de création')) // Filtre sur la date
        ;
    }
}