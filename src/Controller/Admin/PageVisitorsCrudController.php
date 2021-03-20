<?php

namespace App\Controller\Admin;

use App\Entity\PageVisitors;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PageVisitorsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageVisitors::class;
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
