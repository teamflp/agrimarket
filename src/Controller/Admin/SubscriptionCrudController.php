<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

/**
 * CrudController pour gérer les entités Address dans l'interface EasyAdmin.
 */
class SubscriptionCrudController extends AbstractCrudController
{
    /**
    * Retourne le nom complet de la classe de l'entité gérée par ce CrudController.
    *
    */
    public static function getEntityFqcn(): string
    {
        return Subscription::class; // Retourne le nom complet de la classe Subscription
    }

    
    /**
     * Configure les champs affichés dans les listes et les formulaires de création et de modification.
     *
     * @param string $pageName Le nom de la page (index, new, edit, detail).
     *
     * @return iterable Un tableau d'objets Field représentant les champs à afficher.
     */
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('plan','plan'),
            DateTimeField::new('starDateAt', "Date d'inscription"),
            DateTimeField::new('endDateAt', "date de fin d'inscription"),
            AssociationField::new('utilisateur', 'Utilisateur')
            ->setFormTypeOptions([

            'by_reference' => false, // Important pour gérer les relations

        ])
        
        ];
    }
    /**
     * Configure le comportement général du CRUD (Create, Read, Update, Delete) pour l'entité.
     *
     * @param Crud $crud L'objet Crud à configurer.
     *
     * @return Crud L'objet Crud modifié.
     */
    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Subscription')// Définit le label au singulier
        ->setEntityLabelInPlural('Subscriptions')// Définit le label au pluriel
        ->setSearchFields(['name'])// Définit les champs utilisés pour la recherche
        ->setDefaultSort(['plan' => 'ASC'])// Définit le champ et l'ordre de tri par défaut
  ;
  }

    public function configureActions(Actions $actions): Actions
    {
    return $actions
        // Ajoute une action "detail" (afficher les détails) à la page d'index (liste des éléments)
        ->add(Crud::PAGE_INDEX, 'detail')
        // Ajoute une action "detail" (afficher les détails) à la page d'édition (formulaire de modification)
        ->add(Crud::PAGE_EDIT, 'detail')
  ;
  }
}
