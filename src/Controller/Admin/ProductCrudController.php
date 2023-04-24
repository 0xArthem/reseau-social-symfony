<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            BooleanField::new('isActive')->setLabel('Actif'),
            ImageField::new('image')->setBasePath('/assets/images/')
                ->setUploadDir('./public/assets/images/')
                ->setRequired(false)
                ->setLabel('Image'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('slug')->setLabel('URL'),
            AssociationField::new('category')->setLabel('Catégorie'),
            TextEditorField::new('description')->setLabel('Description'),
            TextEditorField::new('moreInformations')->setLabel('Informations'),
            MoneyField::new('price')->setCurrency('EUR')->setLabel('Prix'),
            MoneyField::new('lastPrice')->setCurrency('EUR')->setLabel('Ancien prix')->hideOnIndex(),
            BooleanField::new('isBestSeller')->setLabel('Best Seller'),
            BooleanField::new('isNewArrival')->setLabel('Nouveauté'),
            BooleanField::new('isFeatured')->setLabel('Vedette'),
            BooleanField::new('isSpecialOffer')->setLabel('Offre spéciale'),
        ];
    }
}
