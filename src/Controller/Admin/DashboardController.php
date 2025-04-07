<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Agrimarket');
    }

    /*public function configureMenuItems(): iterable
    {
        $roles = $this->getUser()->getRoles();

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        if (in_array('ROLE_ADMIN', $roles)) {
            // Menus pour l'administrateur
            yield MenuItem::subMenu('Utilisateurs', 'fas fa-users')
                ->setSubItems([
                    MenuItem::linkToCrud('Tous les utilisateurs', 'fas fa-list', User::class),
                    MenuItem::linkToCrud('Ajouter un utilisateur', 'fas fa-plus', User::class)
                        ->setAction('new'),
                ]);

            yield MenuItem::subMenu('Produits', 'fas fa-list')
                ->setSubItems([
                    MenuItem::linkToCrud('Categories', 'fas fa-folder', Category::class),
                    MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class)
                        ->setAction('index'),
                ]);

            yield MenuItem::subMenu('Commandes', 'fas fa-shopping-cart')
                ->setSubItems([
                    MenuItem::linkToCrud('Commandes', 'fas fa-list', Order::class),
                    MenuItem::linkToCrud('Ajouter une commande', 'fas fa-plus', Order::class)
                        ->setAction('new'),
                ]);
        } elseif (in_array('ROLE_FARMER', $roles)) {
            // Menus pour le fermier
            yield MenuItem::subMenu('Produits', 'fas fa-list')
                ->setSubItems([
                    MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class)
                        ->setAction('index'),
                    MenuItem::linkToCrud('Ajouter un produit', 'fas fa-plus', Product::class)
                        ->setAction('new'),
                ]);

            yield MenuItem::subMenu('Commandes', 'fas fa-shopping-cart')
                ->setSubItems([
                    MenuItem::linkToCrud('Commandes', 'fas fa-list', Order::class),
                ]);
        } elseif (in_array('ROLE_USER', $roles)) {
            // Menus pour l'utilisateur
            yield MenuItem::subMenu('Commandes', 'fas fa-shopping-cart')
                ->setSubItems([
                    MenuItem::linkToCrud('Mes commandes', 'fas fa-list', Order::class)
                        ->setController(OrderCrudController::class)
                        ->setQueryParameter('user', $this->getUser()->getId()),
                ]);

            yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);

        }
    }*/

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        yield MenuItem::section('Gestion des produits');
        yield MenuItem::linkToCrud('Produits', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Category::class);

        yield MenuItem::section('Gestion des commandes');
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class);
    }
}