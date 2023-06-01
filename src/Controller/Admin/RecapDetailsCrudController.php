<?php

namespace App\Controller\Admin;

use App\Entity\RecapDetails;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RecapDetailsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RecapDetails::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->setLabel('Id'),
            AssociationField::new('orderProduct')->setLabel('Commande concernée'),
            TextField::new('product')->setLabel('Nom du produit'),
            IntegerField::new('quantity')->setLabel('Quantité du produit'),
            MoneyField::new('price')->setCurrency('EUR')->setLabel('Prix unitaire'),
            MoneyField::new('totalRecap')->setCurrency('EUR')->setLabel('Panier total'),
        ];
    }
}
