<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Promo;
use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Categories;
use App\Entity\CartDetails;
use App\Entity\TagsProduct;
use App\Entity\OrderDetails;
use App\Entity\RelatedProduct;
use App\Entity\ReviewsProduct;
use App\Controller\Admin\OrderCrudController;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // return parent::index();
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration');
    }

    public function configureMenuItems(): iterable
        {
            yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
            
            yield MenuItem::subMenu('Produits', 'fas fa-boxes')->setSubItems([
                MenuItem::linkToCrud('Produits', 'fas fa-boxes', Product::class),
                MenuItem::linkToCrud('Catégories', 'fas fa-folder', Categories::class),
                MenuItem::linkToCrud('Avis', 'fas fa-star', ReviewsProduct::class)
            ]);
            
            yield MenuItem::subMenu('Commandes', 'fas fa-shopping-cart')->setSubItems([
                MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class),
                MenuItem::linkToCrud('Détails des commandes', 'fas fa-info-circle', OrderDetails::class),
                MenuItem::linkToCrud('Transporteurs', 'fas fa-truck', Carrier::class)
            ]);

            yield MenuItem::subMenu('Paniers', 'fas fa-shopping-basket')->setSubItems([
                MenuItem::linkToCrud('Paniers', 'fas fa-shopping-basket', Cart::class),
                MenuItem::linkToCrud('Détails des paniers', 'fas fa-info-circle', CartDetails::class),
            ]);

            yield MenuItem::subMenu('Utilisateurs', 'fas fa-users')->setSubItems([
                MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class),
                MenuItem::linkToCrud('Adresses', 'fas fa-map-marker-alt', Address::class)
            ]);  

            yield MenuItem::subMenu('Blog', 'fas fa-blog')->setSubItems([
                MenuItem::linkToCrud('Articles', 'fas fa-file-alt', Article::class),
                MenuItem::linkToCrud('Catégories', 'fas fa-folder', ArticleCategory::class)
            ]);

            // yield MenuItem::linkToCrud('RelatedProduct', 'fas fa-list', RelatedProduct::class);
            // yield MenuItem::linkToCrud('Tag', 'fas fa-list', TagsProduct::class);
        }
}
