<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('firstname', 'Nom'),
            TextField::new('lastname', 'Prénom'),
            TextField::new('phoneNumber', 'Téléphone'),
            BooleanField::new('isActive', 'Actif'),
            ArrayField::new('roles', 'Rôles'),
            TextField::new('email', 'Email')
            ->setFormTypeOptions([
                'disabled' => $pageName === 'show'
            ]),
        ];
    }

}
