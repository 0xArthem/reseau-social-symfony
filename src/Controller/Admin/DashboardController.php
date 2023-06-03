<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Promo;
use App\Entity\Address;
use App\Entity\Article;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Categories;
use App\Entity\CartDetails;
use App\Entity\TagsProduct;
use App\Entity\Transporter;
use App\Entity\CategoryShop;
use App\Entity\OrderDetails;
use App\Entity\RecapDetails;
use App\Entity\RelatedProduct;
use App\Entity\ReviewsProduct;
use App\Entity\ArticleCategory;
use App\Controller\Admin\OrderCrudController;
use App\Entity\Contact;
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
            
            yield MenuItem::subMenu('E-commerce', 'fas fa-boxes')->setSubItems([
                MenuItem::linkToCrud('Produits', 'fas fa-boxes', Product::class),
                MenuItem::linkToCrud('Catégories', 'fas fa-boxes', CategoryShop::class),
                MenuItem::linkToCrud('Commandes', 'fas fa-boxes', Order::class),
                MenuItem::linkToCrud('Commandes - Détails', 'fas fa-boxes', RecapDetails::class),
                MenuItem::linkToCrud('Transporteurs', 'fas fa-boxes', Transporter::class),
            ]);

            yield MenuItem::subMenu('Utilisateur', 'fas fa-boxes')->setSubItems([
                MenuItem::linkToCrud('Utilisateurs', 'fas fa-boxes', User::class),
                MenuItem::linkToCrud('Adresses', 'fas fa-boxes', Address::class),
            ]);

            yield MenuItem::linkToCrud('Contact', 'fa fa-home', Contact::class);
        }
}
