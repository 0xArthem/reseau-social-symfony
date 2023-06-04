<?php

namespace App\Controller\Admin;

use App\Entity\Info;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Info::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->setLabel('Id'),
            TextField::new('header')->setLabel('Contenu'),
        ];
    }
}
