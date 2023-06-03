<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->hideOnForm()->hideOnIndex(),
            TextField::new('title')->setLabel('Titre'),
            TextEditorField::new('content')->setLabel('Contenu'),
            AssociationField::new('category')->setLabel('Catégorie'),
            BooleanField::new('isActive')->setLabel('En ligne'),
            DateTimeField::new('createdAt')->setLabel('Date de création'),
        ];
    }
}
