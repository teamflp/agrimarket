<?php

namespace App\Controller\Admin;

use App\Entity\ReviewReport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderController extends AbstractCrudController
{
    public static function getEntityFqcn(): String
    {
        return ReviewReport::class;
    }

    public function configureFields(string $reviewReport): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('reason', 'raison'),
            TextField::new('status', 'status'),
            DateTimeField::new('date', 'date de creation'),
            AssociationField::new('User', 'Utilisateur'),
            AssociationField::new('Rating', 'Rating'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('ReviewReport')
        ->setEntityLabelInPlural('ReviewReports')
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