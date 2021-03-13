<?php

namespace App\Controller\Admin;

use App\Entity\Shows;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShowsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shows::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $show = new Shows();
        $show->setUser($this->getUser());

        return $show;
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
