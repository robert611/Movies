<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ShowService;
use App\Entity\Shows;
use App\Entity\ShowLinks;

class ShowController extends AbstractController
{
    /**
     * @Route("/show/{showTableName}/{episodeId}", name="show_index")
     */
    public function index($showTableName, $episodeId = 1): Response
    {
        $showsRepository = $this->getDoctrine()->getRepository(Shows::class);
        
        $showsLinksRepository = $this->getDoctrine()->getRepository(ShowLinks::class);

        $show = $showsRepository->findOneBy(['database_table_name' => $showTableName]);

        if (!$show) {
            $this->addFlash('error', 'Nie ma takiego serialu');

            return $this->redirectToRoute('index');
        }

        $thisEpisode = $showsRepository->findEpisode((string) $showTableName, (int) $episodeId);

        if (!$thisEpisode) {
            $this->addFlash('error', 'Nie udało się nam znaleźć tego odcinka');

            return $this->redirectToRoute('index');
        }

        $showEpisodes = $showsRepository->findShowAllEpisodesBy($showTableName, '');

        $showService = new ShowService($showEpisodes);

        $showSeasonsNumbersWithSeasonFirstEpisodeId = $showService->getShowSeasonsNumbersWithSeasonFirstEpisodeId();

        $episodeSeason = $thisEpisode['season'];

        $showSeasonEpisodes = $showService->getSeasonEpisodes($episodeSeason);

        $linksToEpisode = $showsLinksRepository->findBy(['show_database_table_name' => $showTableName, 'episode_id' => $episodeId]);
 
        return $this->render('show/index.html.twig', [
            'showSeasonsNumbers' => $showSeasonsNumbersWithSeasonFirstEpisodeId,
            'episode' => $thisEpisode,
            'episodes' => $showSeasonEpisodes,
            'show' => $show,
            'links' => $linksToEpisode
        ]);
    }
}
