<?php

namespace App\Controller\Admin;

use App\Entity\Rating;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RatingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rating::class;
    }

    
    public function configureFields(string $rating): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IntegerField::new('score', 'score'),
            TextField::new('comment', 'commentaire'),
            DateTimeField::new('createAt', 'Crée à')
        ];
    }
    
}
