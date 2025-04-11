<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

/**
 * CrudController pour gérer les entités Address dans l'interface EasyAdmin.
 */
class AddressCrudController extends AbstractCrudController
{
    /**
    * Retourne le nom complet de la classe de l'entité gérée par ce CrudController.
    *
    */
    public static function getEntityFqcn(): string
    {
        return Address::class; // Retourne le nom complet de la classe Address
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
            TextField::new('street','rue'),
            TextField::new('city','ville'),
            TextField::new('zipCode','code Postal'),
            TextField::new('country','pays'),
            TextField::new('labe','label')->setRequired(false),
            NumberField::new('latitude','latitude')->setRequired(false),
            NumberField::new('longitude','longitude')->setRequired(false),
            AssociationField::new('users', 'Utilisateur')
                ->setFormTypeOptions([
                    'class' => User::class,
                    'choice_label' => 'email', // Champ à afficher dans le select
                    'multiple' => true, // Permet de sélectionner plusieurs utilisateurs
                    'expanded' => false, // Affiche un select au lieu de cases à cocher
                'by_reference' => false, // Important pour gérer les relations

            ])
                ->setRequired(false),
            
        ];
    }
    /**
     * Configure le comportement général du CRUD (Create, Read, Update, Delete) pour l'entité Address.
     *
     * @param Crud $crud L'objet Crud à configurer.
     *
     * @return Crud L'objet Crud modifié.
     */
    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Address') // Définit le label au singulier
        ->setEntityLabelInPlural('Addresses')// Définit le label au pluriel
        ->setSearchFields(['name'])// Définit les champs utilisés pour la recherche
        ->setDefaultSort(['street' => 'ASC'])// Définit le champ et l'ordre de tri par défaut
  ;
  }
     /**
    * @param Actions $actions
    *
    * @return Actions
    */
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
