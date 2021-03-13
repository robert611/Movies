<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\LatestEpisodes;
use App\Entity\ShowRanking;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $showsRanking = $this->getDoctrine()
            ->getRepository(ShowRanking::class)->findBy([], [], 15);
        
        $showsLatestEpisodes = $this->getDoctrine()
            ->getRepository(LatestEpisodes::class)->getLatestEpisodesWithFilledData(9);

        return $this->render('index/index.html.twig', [
            'showsRanking' => $showsRanking,
            'showsLatestEpisodes' => $showsLatestEpisodes
        ]);
    }
}
