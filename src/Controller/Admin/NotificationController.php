<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
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
        return Notification::class;
    }

    public function configureFields(string $notification): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'titre'),
            TextField::new('content', 'contenu'),
            TextField::new('channel', 'channel'),
            DateTimeField::new('date', 'date de creation'),
            AssociationField::new('Users', 'Utilisateur'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Notification')
        ->setEntityLabelInPlural('Notifications')
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