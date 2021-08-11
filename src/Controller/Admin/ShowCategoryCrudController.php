<?php

namespace App\Controller\Admin;

use App\Entity\ShowCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShowCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShowCategory::class;
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
