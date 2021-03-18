<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ShowPicturesService;
use App\Form\ShowType;
use App\Entity\Shows;
use App\Entity\ShowLinks;
use App\Entity\LatestEpisodes;
use App\Entity\ShowRanking;
use App\Entity\User;

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
    public function createShow(Request $request, EntityManagerInterface $entityManager, ShowPicturesService $showPicturesService): Response
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

            if ($showPicturesService->isNameAlreadyTaken($formData['picture'], $this->getParameter('shows_pictures_directory'))) {
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

            $showPicturesService->uploadPicture($formData['picture'], $this->getParameter('shows_pictures_directory'));

            $this->addFlash('admin_success', 'Show has been created');

            return $this->redirectToRoute('admin_show_create');
        }

        return $this->render('admin/create_show.html.twig', ['form' => $form->createView()]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Movies');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Shows', 'far fa-file-video', Shows::class);
        yield MenuItem::linkToCrud('Latest Episodes', 'fas fa-video', LatestEpisodes::class);
        yield MenuItem::linkToCrud('Shows Ranking', 'fas fa-list', ShowRanking::class);
        yield MenuItem::linkToCrud('Shows Links', 'fa fa-link', ShowLinks::class);
    }
}
