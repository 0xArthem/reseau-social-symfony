<?php

namespace App\Controller\Admin;

use App\Entity\TagsProduct;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TagsProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TagsProduct::class;
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
