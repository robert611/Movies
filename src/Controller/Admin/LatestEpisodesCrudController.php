<?php

namespace App\Controller\Admin;

use App\Entity\LatestEpisodes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LatestEpisodesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LatestEpisodes::class;
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
