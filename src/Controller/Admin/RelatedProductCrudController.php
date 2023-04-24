<?php

namespace App\Controller\Admin;

use App\Entity\RelatedProduct;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RelatedProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RelatedProduct::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
