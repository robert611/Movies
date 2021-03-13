<?php

namespace App\Controller\Admin;

use App\Entity\ShowLinks;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShowLinksCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShowLinks::class;
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
