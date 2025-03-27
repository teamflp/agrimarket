<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController pour gérer les entités Address dans l'interface EasyAdmin.
 */

class CategoryCrudController extends AbstractCrudController
{
    /**
    * Retourne le nom complet de la classe de l'entité gérée par ce CrudController.
    *
    */
    public static function getEntityFqcn(): string
    {
        return Category::class;// Retourne le nom complet de la classe Category
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
            IdField::new('id')-> hideOnForm(),
            TextField::new('name', 'nom'),
            AssociationField::new('product', 'produits'),
           
        ];
    }
    /**
     * Configure le comportement général du CRUD (Create, Read, Update, Delete) pour l'entité Category.
     *
     * @param Crud $crud L'objet Crud à configurer.
     *
     * @return Crud L'objet Crud modifié.
     */
    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Category')// Définit le label au singulier
        ->setEntityLabelInPlural('Categories')// Définit le label au pluriel
        ->setSearchFields(['name'])// Définit les champs utilisés pour la recherche
        ->setDefaultSort(['name' => 'ASC'])// Définit le champ et l'ordre de tri par défaut
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
