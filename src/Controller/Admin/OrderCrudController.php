<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setSearchFields(['reference'])
            ->setDefaultSort(['id' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('user')->setLabel('Client')->setFormTypeOption('disabled', true),
            TextField::new('reference')->hideOnIndex()->setLabel('Référence')->setFormTypeOption('disabled', true),
            // TextField::new('fullName')->hideOnIndex()->setLabel('Adresse'),
            TextField::new('carrierName')->setLabel('Livraison'),
            TextareaField::new('deliveryAddress')->hideOnIndex()->setLabel('Adresse de livraison'),
            TextareaField::new('moreInformations')->hideOnIndex()->setLabel('Informations supplémentaires'),
            DateTimeField::new('createdAt')->setLabel('Date de création')->setFormTypeOption('disabled', true),
            BooleanField::new('isPaid')->setLabel('Payé'),
            IntegerField::new('quantity')->setLabel('Quantité'),
            MoneyField::new('subTotalHT')->setCurrency('EUR')->hideOnIndex()->setLabel('Total HT'),
            MoneyField::new('taxe')->setCurrency('EUR')->hideOnIndex()->setLabel('Taxe - TVA 20%'),
            MoneyField::new('carrierPrice')->setCurrency('EUR')->hideOnIndex()->setLabel('Prix de la livraison'),
            MoneyField::new('subTotalTTC')->setCurrency('EUR')->setLabel('Total TTC'),
            TextField::new('stripeCheckoutSessionId')->hideOnIndex()->setLabel('ID Checkout Stripe')->setFormTypeOption('disabled', true),
            
            BooleanField::new('isIsProcess')->setLabel('En cours'),
            BooleanField::new('isInDelivering')->setLabel('Livraison'),
            BooleanField::new('isDelivered')->setLabel('Livré'),
            BooleanField::new('isInReturn')->setLabel('Retourné'),
            BooleanField::new('isRefunded')->setLabel('Remboursé'),
            BooleanField::new('isCanceled')->setLabel('Annulé'),
        
        ];
    }
}
