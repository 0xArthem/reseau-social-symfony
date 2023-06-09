<?php

namespace App\Controller\Admin;

use App\Entity\ArticleCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégories de l\'article')
            ->setEntityLabelInPlural('Catégories des articles')
            ->setSearchFields(['name'])
            ->setDefaultSort(['id' => 'DESC']);
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
