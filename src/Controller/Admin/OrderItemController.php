<?php

namespace App\Controller\Admin; // Définit le nom pour ce contrôleur dans le répertoire Admin

use App\Entity\OrderItem; // Importe l'entité OrderItem
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; // Classe de base pour les contrôleurs CRUD
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField; // Importe le champ IdField pour afficher et gérer l'identifiant de l'entité
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField; // Importe le champ AssociationField pour gérer les relations entre entités (OrderItem -> Order / OrderItem -> Product)
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField; // Importe le champ NumberField pour afficher et éditer des valeurs numériques
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud; // Importe la configuration CRUD d'EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions; // Importe la configuration des actions

class OrderItemController extends AbstractCrudController
{
    public static function getEntityFqcn(): String // Méthode statique qui retourne le nom de l'entité gérée
    {
        return OrderItem::class; // Indique que contrôleur gère l'entité OrderItem
    }

    public function configureFields(string $orderItem): iterable // Configure les champs qui seront affichés dans l'interface d'administration
    {
        return [
            IdField::new('id')->hideOnForm(), // Champ ID, caché dans le formulaire mais visible dans la liste
            AssociationField::new('Order', 'Commande'), // Champ de relation vers l'entité Order (relation ManyToOne)
            AssociationField::new('Product', 'Produit'), // Champ de relation vers l'entité Product (relation ManyToOne)
            NumberField::new('Quantity', 'Quantite'), // Champ de la quantité d'élément
            NumberField::new('UnitPrice', 'Prix') // Champ du prix a l'unité
        ];
    }

    public function configureCrud(Crud $crud): Crud // Configure les options globales du CRUD
    {
    return $crud
        ->setEntityLabelInSingular('OrderItem') // Label au singulier pour un élément de la commande
        ->setEntityLabelInPlural('OrderItems') // Label au pluriel pour un élément de la commande
        ->setSearchFields(['quantity']) // Champ pour la recherche quantity
        ->setDefaultSort(['quantity' => 'DESC']) // Tri par quantité
    ;
    }

    public function configureActions(Actions $actions): Actions // Configure les actions disponibles dans l'interface
    {
    return $actions
        ->add(Crud::PAGE_INDEX, 'detail') // Ajoute l'action "détail" sur la page d'index (liste des élements de la commande)
        ->add(Crud::PAGE_EDIT, 'detail') //Ajoute l'action "détail" sur la page d'édition
    ;
    }
}