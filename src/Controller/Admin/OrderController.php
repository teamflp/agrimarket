<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderController extends AbstractCrudController
{
    public static function getEntityFqcn(): String
    {
        return Order::class;
    }

    public function configureFields(string $order): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('Users', 'Utilisateur'),
            TextField::new('status', 'status'),
            DateTimeField::new('date', 'date'),
            NumberField::new('value', 'montant')
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Order')
        ->setEntityLabelInPlural('Orders')
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
