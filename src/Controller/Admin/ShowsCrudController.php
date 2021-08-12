<?php

namespace App\Controller\Admin;

use App\Entity\Shows;
use App\Entity\LatestEpisodes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UploadFileService;
use App\Service\EpisodeLinkService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

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
    public function deleteShowDatabaseTable(Request $request, UploadFileService $uploadFileService): Response
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

            if (!$uploadFileService->deleteFile($this->getParameter('shows_pictures_directory') . "/" . $showPictureName)) {
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

    /**
     * @Route("/admin/show/delete/episode/{showDatabaseTableName}/{episodeId}", name="admin_show_delete_episode")
     */
    public function deleteShowEpisode(string $showDatabaseTableName, string $episodeId, EntityManagerInterface $entityManager, EpisodeLinkService $episodeLinkService): Response
    {
        $showRepostiory = $this->getDoctrine()->getRepository(Shows::class);
        $latestEpisodesRepository =  $this->getDoctrine()->getRepository(LatestEpisodes::class);

        if (!$showRepostiory->checkIfTableExists($showDatabaseTableName)) {
            $this->addFlash('admin_error', "Table of the show with the name: {$showDatabaseTableName} does not exist");

            return $this->redirectToRoute('admin_show_episodes_list', ['showDatabaseTableName' => $showDatabaseTableName]);
        }

        $showRepostiory->deleteShowEpisode($showDatabaseTableName, (int) $episodeId);

        $episodeLinkService->deleteShowEpisodeLinks($showDatabaseTableName, (int) $episodeId);

        $latestEpisodesRepository->deleteLatestEpisode($showDatabaseTableName, (int) $episodeId);

        $this->addFlash('admin_success', "Episode of table ${showDatabaseTableName} with an id ${episodeId} has been deleted");

        return $this->redirectToRoute('admin_show_episodes_list', ['showDatabaseTableName' => $showDatabaseTableName]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('database_table_name'),
            TextField::new('picture'),
            TextEditorField::new('description'),
            AssociationField::new('studio'),
            AssociationField::new('category'),
            AssociationField::new('themes'),
            DateTimeField::new('created_at'),
            DateTimeField::new('updated_at')
        ];
    }

}
