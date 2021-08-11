<?php

namespace App\Controller\Admin;

use App\Entity\ShowTheme;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShowThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShowTheme::class;
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
