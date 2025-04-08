<?php

namespace App\Controller\Admin; // Définit le nom pour ce contrôleur dans le répertoire Admin

use App\Entity\Order; // Importe l'entité Order
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Classe de base pour les contrôleurs CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importe le champ IdField pour afficher et gérer l'identifiant de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Importe le champ AssociationField pour gérer les relations entre entités (Order -> User)
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField; // Importe le champ ChoiceField pour afficher une liste déroulante avec des choix prédéfinis
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField; // Importe le champ DateTimeField pour afficher et éditer des dates/heures
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; // Importe le champ NumberField pour afficher et éditer des valeurs numériques
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; // Importe la configuration CRUD d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions; // Importe la configuration des actions
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters; // Importe la classe Filters pour configurer les filtres dans la liste de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter; // Importe le filtre ChoiceFilter pour ajouter un filtre de type liste déroulante dans l'interface
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter; // Importe le filtre DateTimeFilter pour ajouter un filtre de plage de dates dans l'interface

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): String // Méthode statique qui retourne le nom de l'entité gérée
    {
        return Order::class; // Indique que contrôleur gère l'entité Order
    }

    public function configureFields(string $order): iterable // Configure les champs qui seront affichés dans l'interface d'administration
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire mais visible dans la liste
            AssociationField::new('Users', 'Utilisateur'), // Champ de relation vers l'entité Users (relation ManyToOne)
            ChoiceField::new('status', 'Statut')->setChoices([ // Champ de canal avec une liste déroulante : pedding / shipped / delivered
                'pedding' => 'En Attente',
                'shipped' => 'Expédiée',
                'delivered' => 'Livré',
            ]),
            DateTimeField::new('date', 'Date'), // Champ de la Date de la commande
            NumberField::new('value', 'Montant') // Champ de Montant de la commande, champ numérique
        ];
    }

    public function configureCrud(Crud $crud): Crud // Configure les options globales du CRUD
    {
        return $crud
            ->setEntityLabelInSingular('Order') // Label au singulier pour une commande
            ->setEntityLabelInPlural('Orders') // Label au pluriel pour les commandes
            ->setSearchFields(['status']) // Champs pour la recherche status
            ->setDefaultSort(['date' => 'DESC']) // Tri par date
            ;
    }

    public function configureActions(Actions $actions): Actions // Configure les actions disponibles dans l'interface
    {
        return $actions
            ->add(Crud::PAGE_INDEX, 'detail') // Ajoute l'action "détail" sur la page d'index (liste des commandes)
            ->add(Crud::PAGE_EDIT, 'detail') // Ajoute l'action "détail" sur la page d'édition
            ;
    }

    public function configureFilters(Filters $filters): Filters // Configure les filtres dans l'interface
    {
        return $filters
            ->add(ChoiceFilter::new('status', 'statut')->setChoices([ // Filtre pour le statut avec les mêmes choix que ChoiceField
                'pedding' => 'En Attente',
                'shipped' => 'Expédiée',
                'delivered' => 'Livré',
            ]))
            ->add(DateTimeFilter::new('date', 'Date')) // Filtre pour la date, permettant de filtrer par plage de dates
            ;
    }
}
