<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SubscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    
    public function configureFields(string $subscription): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('plan','plan'),
            DateTimeField::new('starDateAt', "Date d'inscription"),
            DateTimeField::new('endDateAt', "date de fin d'inscription"),
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Subscription')
        ->setEntityLabelInPlural('Subscriptions')
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
