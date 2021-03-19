<?php

namespace App\Controller\Admin;

use App\Entity\Shows;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ShowPicturesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route("/admin/show/delete/table", name="admin_show_delete_table")
     */
    public function deleteShowDatabaseTable(Request $request, ShowPicturesService $showPicturesService): Response
    {
        $showRepostiory = $this->getDoctrine()->getRepository(Shows::class);

        $showDatabaseTableName = (string) $request->request->get('show_database_table_name');

        if (!$showRepostiory->checkIfTableExists($showDatabaseTableName)) {
            $this->addFlash('admin_error', "Table of the show with the name: {$showDatabaseTableName} does not exist");

            return $this->redirectToRoute('admin_show_create');
        }

        $show = $showRepostiory->findOneBy(['database_table_name' => $showDatabaseTableName]);

        if ($show) {
            $showPictureName = $show->getPicture();

            if (!$showPicturesService->deletePicture($this->getParameter('shows_pictures_directory') . "/" . $showPictureName)) {
                $this->addFlash('admin_error', "For some reason show picture was not deleted");
            } else {
                $this->addFlash('admin_success', "Picture of the show: {$showDatabaseTableName} has been deleted");
            }
        } else {
            $this->addFlash('admin_error', "Record for this show does not exist in shows table, so picture could not be deleted!");
        }

        $showRepostiory->deleteShowDatabaseTable($showDatabaseTableName);

        $this->addFlash('admin_success', "Show database table with name: {$showDatabaseTableName} has been deleted");

        return $this->redirectToRoute('admin_show_create');
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
