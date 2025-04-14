<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Plan;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Entity\Rating;
use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Subscription;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin_index')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Agrimarket');
    }

    /*
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'home');

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestion des utilisateurs');
            yield MenuItem::linkToCrud('Users', 'box', User::class);
        }

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_FARMER')) {
            yield MenuItem::section('Gestion des produits');
            yield MenuItem::subMenu('Produits', 'fa fa-box')->setSubItems([
                MenuItem::linkToCrud('Products', 'box', ProductCrudController::class),
                MenuItem::linkToCrud('Categories', 'list', Category::class),
                MenuItem::linkToCrud('Ratings', 'start', Rating::class),
                MenuItem::linkToCrud('Coupons', 'percent', Coupon::class)
                    ->setAction('index'),
            ]);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestion des abonnements');
            yield MenuItem::subMenu('Abonnements', 'fa fa-plan')->setSubItems([
                MenuItem::linkToCrud('Subscriptions', 'box', Subscription::class),
                MenuItem::linkToCrud('Plans', 'list', Plan::class),
            ]);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestion des commandes');
            yield MenuItem::subMenu('Commandes', 'fa fa-shopping-cart')->setSubItems([
                MenuItem::linkToCrud('Orders', 'box', OrderCrudController::class),
                MenuItem::linkToCrud('Order Items', 'list', OrderItem::class),
            ]);
            yield MenuItem::linkToCrud('Addresses', 'box', Address::class);
        }
    }
    */

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'home');

            yield MenuItem::section('Gestion des utilisateurs');
            yield MenuItem::linkToCrud('Users', 'box', User::class);

            yield MenuItem::section('Gestion des produits');
            yield MenuItem::subMenu('Produits', 'fa fa-box')->setSubItems([
                MenuItem::linkToCrud('Products', 'box', Product::class),
                MenuItem::linkToCrud('Categories', 'list', Category::class),
                MenuItem::linkToCrud('Ratings', 'start', Rating::class),
                MenuItem::linkToCrud('Coupons', 'percent', Coupon::class)
                    ->setAction('index'),
            ]);

            yield MenuItem::section('Gestion des abonnements');
            yield MenuItem::subMenu('Abonnements', 'fa fa-plan')->setSubItems([
                MenuItem::linkToCrud('Subscriptions', 'box', Subscription::class),
                MenuItem::linkToCrud('Plans', 'list', Plan::class),
            ]);

            yield MenuItem::section('Gestion des commandes');
            yield MenuItem::subMenu('Commandes', 'fa fa-shopping-cart')->setSubItems([
                MenuItem::linkToCrud('Orders', 'box', Order::class),
                MenuItem::linkToCrud('Order Items', 'list', OrderItem::class),
            ]);
            yield MenuItem::linkToCrud('Addresses', 'box', Address::class);
    }

}