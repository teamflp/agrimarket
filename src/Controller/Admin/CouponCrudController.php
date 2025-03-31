<?php

namespace App\Controller\Admin;

use App\Entity\Coupon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController pour gérer les entités Coupon dans l'interface EasyAdmin.
 */
class CouponCrudController extends AbstractCrudController
{   /**
    * Retourne le nom complet de la classe de l'entité gérée par ce CrudController.
    *
    */
    public static function getEntityFqcn(): string
    {
        return Coupon::class;// Retourne le nom complet de la classe Coupon
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
            TextField::new('code','code'),
            ChoiceField::new('discountType','promotion')
            ->setChoices([
                'Pourcentage' => 'percentage',
                'Fixe' => 'fixed'
            ]),
            NumberField::new('value','valeur'),
            NumberField::new('usageLimit', "Limite d'utilisation")
                ->setRequired(false),
            DateTimeField::new('expirationDate', "Date d'expiration"),
            NumberField::new('usedCount', "promotion utilisée"),
            AssociationField::new('user', 'Utilisateur')
                ->setFormTypeOptions([
                    'by_reference' => false, // Optionnel selon votre logique
                ]),
            AssociationField::new('order', 'commande')
                ->setFormTypeOptions([
                    'by_reference' => false, // Optionnel selon votre logique
                ]),

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
        ->setEntityLabelInSingular('Coupon')// Définit le label au singulier
        ->setEntityLabelInPlural('Coupons')// Définit le label au pluriel
        ->setSearchFields(['name'])// Définit les champs utilisés pour la recherche
        ->setDefaultSort(['code' => 'ASC'])// Définit le champ et l'ordre de tri par défaut
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
