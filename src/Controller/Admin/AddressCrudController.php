<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Address::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('Id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('user')->setLabel('Utilisateur'),
            TextField::new('title')->setLabel('Nom'),
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('company')->setLabel('Entreprise'),
            TextEditorField::new('address')->setLabel('Adresse'),
            TextField::new('postalcode')->setLabel('Code postal'),
            TextField::new('country')->setLabel('Pays'),
            TextField::new('phone')->setLabel('Téléphone'),
            TextField::new('city')->setLabel('Ville'),
        ];
    }
}
