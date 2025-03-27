<?php

namespace App\Controller\Admin; // Définit le nom pour ce contrôleur dans le répertoire Admin

use App\Entity\Product; // Importe l'entité Product
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Classe de base pour les contrôleurs CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importe le champ IdField pour afficher et gérer l'identifiant de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField; // Importe le champ texte
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; // Importe le champ NumberField pour afficher et éditer des valeurs numériques
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Importe le champ AssociationField pour gérer les relations entre entités (Product -> Category / Product -> User)
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; // Importe la configuration CRUD d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions; // Importe la configuration des actions
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters; // Importe la classe Filters pour configurer les filtres dans la liste des produits
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter; // Importe le filtre EntityFilter pour filtrer par entités liées
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter; // Importe le filtre NumericFilter pour filtrer par valeurs numériques

class ProductController extends AbstractCrudController
{
    public static function getEntityFqcn(): String // Méthode statique qui retourne le nom de l'entité gérée
    {
        return Product::class; // Indique que contrôleur gère l'entité Product
    }

    public function configureFields(string $product): iterable // Configure les champs qui seront affichés dans l'interface d'administration
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire mais visible dans la liste
            TextField::new('name', 'Nom'), // Champ texte pour nom du Produit
            TextField::new('slug', 'Slug'), // Champ texte pour le slug
            TextField::new('description', 'Description'), // Champ texte pour la description du produit
            NumberField::new('price', 'Prix'), // Champ numérique pour le prix du produit
            NumberField::new('quantity', 'Quantite'), // Champ numérique pour la quantité du produit
            AssociationField::new('Category', 'Catégorie'), // Champ de relation vers l'entité Category (relation ManyToOne)
            AssociationField::new('User', 'Utilisateur'), // Champ de relation vers l'entité User (relation ManyToOne)
        ];
    }

    public function configureCrud(Crud $crud): Crud // Configure les options globales du CRUD
    {
    return $crud
        ->setEntityLabelInSingular('Product') // Label au singulier pour un produit
        ->setEntityLabelInPlural('Products') // Label au pluriel pour les produits
        ->setSearchFields(['name']) // Champs pour la recherche
        ->setDefaultSort(['name' => 'DESC']) // Tri décroissant
    ;
    }

    public function configureActions(Actions $actions): Actions // Configure les actions disponibles dans l'interface
    {
    return $actions
        ->add(Crud::PAGE_INDEX, 'detail') // Ajoute l'action "détail" sur la page d'index (liste des produits)
        ->add(Crud::PAGE_EDIT, 'detail') //Ajoute l'action "détail" sur la page d'édition
    ;
    }

    public function configureFilters(Filters $filters): Filters // Configure les filtres disponibles dans la liste des produits
    {
    return $filters
        ->add(EntityFilter::new('category', 'Catégorie')) // Filtre sur la catégorie, permet de sélectionner une catégorie spécifique
        ->add(NumericFilter::new('price', 'Prix')) // Filtre sur le prix, permet de filtrer par plage
        ->add(NumericFilter::new('quantity', 'Quantité')) // Filtre sur la quantité, permet de filtrer par plage
    ;
    }
}