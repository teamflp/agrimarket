<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    
    public function configureFields(string $category): iterable
    {
        return [
            IdField::new('id')-> hideOnForm(),
            TextField::new('name', 'nom'),
           
        ];
    }

    public function configureCrud(Crud $crud): Crud
  {
  return $crud
  ->setEntityLabelInSingular('Catégorie')
  ->setEntityLabelInPlural('Catégories')
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
