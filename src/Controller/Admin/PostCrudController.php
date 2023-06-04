<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Publication')
            ->setEntityLabelInPlural('Publications')
            ->setSearchFields(['title'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->hideOnIndex()->hideOnForm(),
            TextField::new('title')->setLabel('Titre'),
            TextField::new('slug')->setLabel('URL')->hideOnIndex(),
            TextEditorField::new('content')->setLabel('Contenu'),
            AssociationField::new('user')->setLabel('Utilisateur'),
            BooleanField::new('isPinned')->setLabel('Epinglé')->hideOnIndex(),
            DateTimeField::new('createdAt')->setLabel('Date de création'),
            UrlField::new('link')->setLabel('Lien')->hideOnIndex(),
            AssociationField::new('posttag')->setLabel('Catégorie(s)'),
        ];
    }
}
