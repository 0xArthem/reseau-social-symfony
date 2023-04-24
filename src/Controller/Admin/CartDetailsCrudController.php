<?php

namespace App\Controller\Admin;

use App\Entity\CartDetails;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CartDetailsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CartDetails::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Panier - Détails')
            ->setEntityLabelInPlural('Panier - Détails')
            ->setSearchFields(['Carts'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('Carts')->setLabel('Panier concerné')->setFormTypeOption('disabled', true),
            TextField::new('productName')->setLabel('Nom du produit'),
            MoneyField::new('productPrice')->setCurrency('EUR')->setLabel('Prix /u du produit'),
            IntegerField::new('quantity')->setLabel('Quantité'),
            MoneyField::new('subTotalHT')->setCurrency('EUR')->setLabel('Total HT'),
            MoneyField::new('taxe')->setCurrency('EUR')->setLabel('Taxe - TVA 20%'),
            MoneyField::new('subTotalTTC')->setCurrency('EUR')->setLabel('Total TTC'),
        ];
    }
}
