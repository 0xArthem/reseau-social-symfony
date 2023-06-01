<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->setLabel('Id'),
            TextField::new('reference')->setLabel('Reference'),
            AssociationField::new('user')->setLabel('Client'),
            DateTimeField::new('createdAt')->setLabel('Date de la commande'),
            TextField::new('transporterName')->setLabel('Livraison')->hideOnIndex(),
            MoneyField::new('transporterPrice')->setCurrency('EUR')->setLabel('Prix de la livraison')->hideOnIndex(),
            TextField::new('delivery')->setLabel('Adresse de livraison')->hideOnIndex(),
            TextField::new('stripe_session_id')->setLabel('Stripe')->hideOnIndex(),
            TextField::new('paypal_order_id')->setLabel('Paypal')->hideOnIndex(),
            BooleanField::new('isPaid')->setLabel('Payée'),
            TextField::new('method')->setLabel('Methode'),
        ];
    }
}
