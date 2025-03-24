<?php

namespace App\Controller\Admin;

use App\Entity\Coupon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CouponCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Coupon::class;
    }

    
    public function configureFields(string $coupon): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            NumberField::new('code','code'),
            TextField::new('discountType','promotion'),
            NumberField::new('value','valeur'),
            DateTimeField::new('usageLimit', "date d'expiration"),
            DateTimeField::new('usedCount', "promotion utilisée"),

        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInSingular('Coupon')
        ->setEntityLabelInPlural('Coupons')
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
