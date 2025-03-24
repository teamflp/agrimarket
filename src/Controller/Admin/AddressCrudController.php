<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class AddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Address::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('street','rue'),
            TextField::new('city','ville'),
            TextField::new('zipCode','code Postal'),
            TextField::new('label','label'),
            NumberField::new('latitude','latitude'),
            NumberField::new('longitude','longitude'),
            AssociationField::new('Users', 'Utilisateur'),


            
        ];
    }
    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Address')
        ->setEntityLabelInPlural('Addresses')
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
