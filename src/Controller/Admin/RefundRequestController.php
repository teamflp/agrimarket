<?php

namespace App\Controller\Admin; // Définit le nom pour ce contrôleur dans le répertoire Admin

use App\Entity\RefundRequest; // Importe l'entité RefundRequest
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Classe de base pour les contrôleurs CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importe le champ IdField pour afficher et gérer l'identifiant de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Importe le champ texte
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // Importe la classe Filters pour configurer les filtres dans la liste des demandes
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Importe le champ DateTimeField pour afficher et éditer des dates/heures
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Importe le champ AssociationField pour gérer les relations entre entités (RefundRequest -> User)
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; // Importe la configuration CRUD d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions; // Importe la configuration des actions
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters; // Importe le filtre ChoiceFilter pour filtrer par une liste de choix
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter; // Importe le filtre ChoiceFilter pour filtrer par une liste de choix (statut)
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter; // Importe le filtre DateTimeFilter pour filtrer par plage de dates

class RefundRequestController extends AbstractCrudController
{
    public static function getEntityFqcn(): String // Méthode statique qui retourne le nom de l'entité gérée
    {
        return RefundRequest::class; // Indique que contrôleur gère l'entité RefundRequest
    }

    public function configureFields(string $RefundRequest): iterable // Configure les champs qui seront affichés dans l'interface d'administration
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire mais visible dans la liste
            TextField::new('reason', 'Raison'), // Champ texte pour la raison de la demande
            ChoiceField::new('status', 'Statut')->setChoices([ //Champ de canal avec une liste déroulante
                'pending' => 'En attente',
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
            ]),
            TextField::new('message', 'Message'), // Champ texte pour le message de la demande
            DateTimeField::new('date', 'Date de creation'), // Champ date pour la date de création de la demande
            AssociationField::new('User', 'Utilisateur'), // Champ de la relation avec l'entité User
        ];
    }

    public function configureCrud(Crud $crud): Crud // Configure les options globales du CRUD
    {
    return $crud
        ->setEntityLabelInSingular('RefundRequest') // Label au singulier pour RefundRequest
        ->setEntityLabelInPlural('RefundRequests') // Label au pluriel pour RefundRequest
        ->setSearchFields(['reason']) // Champs pour la recherche
        ->setDefaultSort(['date' => 'DESC']) // Tri décroissant
    ;
    }

    public function configureActions(Actions $actions): Actions // Configure les actions disponibles dans l'interface
    {
    return $actions
        ->add(Crud::PAGE_INDEX, 'detail') // Ajoute l'action "détail" sur la page d'index (liste des demandes)
        ->add(Crud::PAGE_EDIT, 'detail') // Ajoute l'action "détail" sur la page d'édition
    ;
    }
    
    public function configureFilters(Filters $filters): Filters // Configure les filtres disponibles dans la liste des demandes
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([ // Filtre sur le statut avec une liste déroulante des mêmes choix que ChoiceField
                'pending' => 'En attente', 
                'approved' => 'Approuvé',
                'rejected' => 'Rejeté',
            ]))
            ->add(DateTimeFilter::new('date', 'Date de création')) // Filtre sur la date
        ;
    }
}