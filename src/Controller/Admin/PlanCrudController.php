<?php

namespace App\Controller\Admin;

use App\Entity\Plan;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PlanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plan::class;
    }

    
    public function configureFields(string $plan): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'nom'),
            TextEditorField::new('description'),
            NumberField::new('price','prix'),
            NumberField::new('price','prix'),
            NumberField::new('maxProducts','Produits maximum'),


        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Plan')
        ->setEntityLabelInPlural('Plans')
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
