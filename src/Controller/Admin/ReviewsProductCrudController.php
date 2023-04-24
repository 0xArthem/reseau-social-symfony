<?php

namespace App\Controller\Admin;

use App\Entity\ReviewsProduct;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ReviewsProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReviewsProduct::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis')
            ->setEntityLabelInPlural('Avis')
            ->setSearchFields(['note'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            IntegerField::new('note')->setFormTypeOption('disabled', true),
            TextEditorField::new('comment')->setFormTypeOption('disabled', true),
            AssociationField::new('user')->setFormTypeOption('disabled', true),
            AssociationField::new('product')->setFormTypeOption('disabled', true),
            BooleanField::new('isActive')->setLabel('Actif')
        ];
    }
}
