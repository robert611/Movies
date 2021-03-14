<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\LatestEpisodes;
use App\Entity\ShowRanking;
use App\Entity\Shows;

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

    /**
     * @Route("/api/episodes/fetch/{searchedTerm}", name="api_episodes_fetch")
     */
    public function fetchEpisodes($searchedTerm): JsonResponse
    {
        $showsRepository = $this->getDoctrine()->getRepository(Shows::class);
         
        $shows = $showsRepository->findAll();

        $episodes = array();

        foreach ($shows as $show)
        {
            $temporaryEpisodes = $showsRepository->findShowAllEpisodesBy($show->getDatabaseTableName(), $searchedTerm);

            foreach ($temporaryEpisodes as &$episode)
            {
                $episode['picture'] = $show->getPicture();
                $episode['table_name'] = $show->getDatabaseTableName();
                $episode['name'] = $show->getName();
            }

            $episodes = array_merge($episodes, $temporaryEpisodes);
        }

        return new JsonResponse($episodes);
    }
}
