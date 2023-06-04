<?php

namespace App\Controller\Admin;

use App\Entity\ArticleCategory;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class ArticleCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleCategory::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->setLabel('Id'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('slug')->setLabel('URL'),
            CollectionField::new('articles')->setLabel('Articles'),
        ];
    }
}
