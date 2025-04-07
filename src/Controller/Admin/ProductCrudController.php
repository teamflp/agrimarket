<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            // Clé primaire, on peut la masquer en formulaire
            IdField::new('id')->hideOnForm(),

            TextField::new('name', 'Nom du produit'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('name'),
            NumberField::new('quantity', 'Quantité disponible')
                ->setFormTypeOptions([
                    'html5' => true,
                    'attr' => [
                        'min' => 0,
                        'step' => 1,
                    ],
                ]),
            MoneyField::new('price', 'Prix')->setCurrency('XOF')
                ->setStoredAsCents(false)
                ->setNumDecimals(0)
                ->setFormTypeOptions([
                    'grouping' => true,
                    'scale' => 2,
                ]),

            // Description en format "texte long"
            TextEditorField::new('description', 'Description'),

            ImageField::new('illustration', 'Illustration')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),

            // Quantité
            IntegerField::new('quantity', 'Quantity'),

            // Relation ManyToOne vers Category
            // => AssociationField, on peut filtrer, configurer un "autocomplete", etc.
            AssociationField::new('category', 'Catégorie')
                ->setRequired(true)
                ->setHelp('La catégorie du produit'),


            // Relation ManyToOne vers User (farmer)
            AssociationField::new('farmer', 'Farmer'),
        ];
    }
}
