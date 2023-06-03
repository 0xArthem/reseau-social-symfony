<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->hideOnForm()->hideOnIndex(),
            TextField::new('email')->setLabel('Adresse électronique'),
            ArrayField::new('roles')->setLabel('Roles'),
            TextField::new('username')->setLabel('Pseudo'),
            TextField::new('firstname')->hideOnIndex()->setLabel('Prénom'),
            TextField::new('lastname')->hideOnIndex()->setLabel('Nom'),
            TextareaField::new('bio')->hideOnIndex()->setLabel('Bio'),
            TextField::new('localisation')->setLabel('Localisation'),
            CollectionField::new('posts')->setLabel('Publications'),
            AssociationField::new('addresses')->setLabel('Adresses'),
            CollectionField::new('orders')->setLabel('Commandes')->hideOnIndex(),
        ];
    }
}
