<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class AddressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Address::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Adresse')
            ->setEntityLabelInPlural('Adresses')
            ->setSearchFields(['fullName'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('user')->setLabel('Utilisateur'),
            TextField::new('fullName')->setLabel('Nom de l\'adresse'),
            TextEditorField::new('address')->setLabel('Adresse'),
            TextField::new('complement')->setLabel('Complément'),
            TextField::new('company')->setLabel('Entreprise'),
            IntegerField::new('phone')->setLabel('Numéro de téléphone'),
            TextField::new('city')->setLabel('Ville'),
            IntegerField::new('codePostal')->setLabel('Code postal'),
            TextField::new('country')->setLabel('Pays'),
        ];
    }
}
