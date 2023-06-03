<?php

namespace App\Controller\Admin;

use App\Entity\CategoryShop;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryShop::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->hideOnForm()->hideOnIndex(),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('slug')->setLabel('URL'),
            CollectionField::new('products')->setLabel('Produits'),
        ];
    }
}
