<?php

namespace App\Controller\Admin;

use App\Entity\ShowRanking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShowRankingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShowRanking::class;
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
