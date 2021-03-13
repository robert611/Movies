<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Movies');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);

        yield MenuItem::section('Shows Stuff');
        yield MenuItem::linkToCrud('Shows', 'fas fa-list', Shows::class);
        yield MenuItem::linkToCrud('Latest Episodes', 'fas fa-list', LatestEpisodes::class);
        yield MenuItem::linkToCrud('Shows Ranking', 'fas fa-list', ShowRanking::class);
        yield MenuItem::linkToCrud('Shows Links', 'fas fa-list', ShowLinks::class);
    }
}
