<?php

namespace App\Controller\Admin;

use App\Entity\OrderItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderController extends AbstractCrudController
{
    public static function getEntityFqcn(): String
    {
        return OrderItem::class;
    }

    public function configureFields(string $orderItem): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('Order', 'Order'),
            AssociationField::new('Product', 'Produit'),
            NumberField::new('value', 'quantite'),
            NumberField::new('value', 'prix')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('OrderItem')
        ->setEntityLabelInPlural('OrderItems')
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