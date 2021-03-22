<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UploadFileService;
use App\Service\AdminVisitorService;
use App\Service\EpisodeLinkService;
use App\Form\ShowType;
use App\Form\EpisodeType;
use App\Entity\Shows;
use App\Entity\ShowLinks;
use App\Entity\LatestEpisodes;
use App\Entity\ShowRanking;
use App\Entity\User;
use App\Entity\Visitor;
use App\Entity\PageVisitors;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/show/create", name="admin_show_create")
     */
    public function createShow(Request $request, EntityManagerInterface $entityManager, UploadFileService $uploadFileService): Response
    {
        $form = $this->createForm(ShowType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $showRepostiory = $this->getDoctrine()->getRepository(Shows::class);

            $formData = $form->getData();

            if ($showRepostiory->checkIfTableExists($formData['original_name'])) {
                $this->addFlash('admin_error', "Table for show with name: {$formData['original_name']} already exists");

                return $this->redirectToRoute('admin_show_create');
            }

            if ($uploadFileService->isNameAlreadyTaken($formData['picture'], $this->getParameter('shows_pictures_directory'))) {
                $this->addFlash('admin_error', "Picture with given name already exists");

                return $this->redirectToRoute('admin_show_create');
            }

            $show = new Shows();
            $show->setName($formData['name']);
            $show->setDatabaseTableName($formData['original_name']);
            $show->setPicture($form->get('picture')->getData()->getClientOriginalName());
            $show->setDescription($formData['description']);
            $show->setUser($this->getUser());
            $show->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
            $show->setUpdatedAt(null);

            $entityManager->persist($show);

            $entityManager->flush();

            $showRepostiory->createShowTable($formData['original_name']);

            $uploadFileService->uploadFile($formData['picture'], $this->getParameter('shows_pictures_directory'));

            $this->addFlash('admin_success', 'Show has been created');

            return $this->redirectToRoute('admin_show_create');
        }

        return $this->render('admin/show/create_show.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/show/list", name="admin_show_list")
     */
    public function listShows(): Response
    {
        $showRepostiory = $this->getDoctrine()->getRepository(Shows::class);

        $shows = $showRepostiory->findAll();

        return $this->render('admin/show/list_shows.html.twig', ['shows' => $shows]);
    }

    /**
     * @Route("/admin/show/episode/create/{showDatabaseTableName}", name="admin_show_episode_create")
     */
    public function createShowEpisode(string $showDatabaseTableName, Request $request, EntityManagerInterface $entityManager, EpisodeLinkService $episodeLinkService): Response
    {
        $form = $this->createForm(EpisodeType::class);
        $form->handleRequest($request);

        $showRepostiory = $this->getDoctrine()->getRepository(Shows::class);
    
        if (!$showRepostiory->checkIfTableExists($showDatabaseTableName)) {
            $this->addFlash('admin_error', "Show with database table name: {$showDatabaseTableName} does not exist");

            return $this->redirectToRoute('admin_show_list');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $formData['user_id'] = $this->getUser()->getId();

            if (!$showRepostiory->saveShowEpisode($showDatabaseTableName, $formData)) {
                $this->session->getFlashBag()->add('admin_error', 'Episode could not be added, due to problem with sql query');

                return $this->render('admin/show/create_episode.html.twig', ['showDatabaseTableName' => $showDatabaseTableName, 'form' => $form->createView()]);
            }

            $episode = $showRepostiory->getLastAddedShowEpisode($showDatabaseTableName);

            $episodeLinkService->saveEpisodeLinks($request, $showDatabaseTableName, $episode['id']);

            $latestEpisode = new LatestEpisodes();

            $latestEpisode->setShowDatabaseTableName($showDatabaseTableName);
            $latestEpisode->setEpisodeId($episode['id']);

            $entityManager->persist($latestEpisode);

            $entityManager->flush();

            $this->addFlash('admin_success', 'Episode has been successfuly added');

            return $this->redirectToRoute('admin_show_episode_create', ['showDatabaseTableName' => $showDatabaseTableName, 'form' => $form->createView()]);
        }
        
        return $this->render('admin/show/create_episode.html.twig', ['showDatabaseTableName' => $showDatabaseTableName, 'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/visitors/page/filtered/{date}", name="admin_visitors_page_filtered")
     */
    public function filteredPageVisitors(?string $date = null, Request $request, AdminVisitorService $adminVisitorService): Response
    {
        $orderBy = $request->query->get('orderBy');

        if (is_null($date)) {
            $date = $this->getDoctrine()->getRepository(PageVisitors::class)->findAll()[0]->getDate();
        } else {
            $date = new \DateTime($date);
        }

        $pageVisitors = $adminVisitorService->getPageVisitorsBy($orderBy, $date);

        return $this->render('admin/filtered_page_visitors.html.twig', ['pageVisitors' => $pageVisitors, 'date' => $date, 'orderBy' => $orderBy]);
    }

    /**
     * @Route("/admin/upload/file", name="admin_upload_file")
     */
    public function uploadFile(Request $request, UploadFileService $uploadFileService)
    {
        $path = $request->request->get('path');
        $file = $request->files->get('file');

        $pathToFolderToWriteFileIn = $this->getParameter('pictures_directory') . "/" . $path;

        if ($uploadFileService->isNameAlreadyTaken($file, $pathToFolderToWriteFileIn)) {
            $this->addFlash('admin_error', "File with given name already exists");

            return $this->redirectToRoute('admin');
        }

        if (!$uploadFileService->uploadFile($file, $pathToFolderToWriteFileIn)) {
            $this->addFlash('admin_error', 'Failure, file has not been uploaded');
        } else {
            $this->addFlash('admin_success', 'File has been uploaded');
        }

        return $this->redirectToRoute('admin');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Movies')
            ->disableUrlSignatures();
    }   

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Shows', 'far fa-file-video', Shows::class);
        yield MenuItem::linkToCrud('Latest Episodes', 'fas fa-video', LatestEpisodes::class);
        yield MenuItem::linkToCrud('Shows Ranking', 'fas fa-list', ShowRanking::class);
        yield MenuItem::linkToCrud('Shows Links', 'fa fa-link', ShowLinks::class);
        yield MenuItem::linkToCrud('Visitors', 'fas fa-user-plus', Visitor::class);
        yield MenuItem::linkToCrud('Page Visitors', 'fas fa-address-book', PageVisitors::class);
    }
}
