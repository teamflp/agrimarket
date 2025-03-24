<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderController extends AbstractCrudController
{
    public static function getEntityFqcn(): String
    {
        return Product::class;
    }

    public function configureFields(string $product): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'nom'),
            TextField::new('slug', 'slug'),
            TextField::new('description', 'description'),
            NumberField::new('value', 'prix'),
            NumberField::new('value', 'quantite'),
            AssociationField::new('Category', 'Categorie'),
            AssociationField::new('User', 'Utilisateur'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Product')
        ->setEntityLabelInPlural('Products')
        ->setSearchFields(['name'])
        ->setDefaultSort(['name' => 'ASC'])
    ;
    }

    public function configureActions(Actions $actions): Actions
    {
    return $actions
        ->add(Crud::PAGE_INDEX, 'detail')
        ->add(Crud::PAGE_EDIT, 'detail')
    ;
    }
}