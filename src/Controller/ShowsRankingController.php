<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\ShowRanking;
use App\Entity\Shows;

class ShowsRankingController extends AbstractController
{
    /**
     * @Route("/api/ranking/find/shows/to/compare", name="ranking_find_shows_to_compare")
     */
    public function findShowsToCompare(): Response
    {
        $showsRepository = $this->getDoctrine()->getRepository(Shows::class);

        $shows = $showsRepository->findAll();

        $showsToCompare = array(); 

        while (count($showsToCompare) < 2)
        {
            $rand = rand(0, count($shows) - 1);

            $randomShow = $shows[$rand];

            if (isset($showsToCompare[0])) 
            {
                if ($showsToCompare[0]->getId() !== $randomShow->getId())
                {
                    $showsToCompare[] = $randomShow;
                }
            }
            else
            {
                $showsToCompare[] = $randomShow;
            }
        }

        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'description', 'createdAt', 'updatedAt']
        ];

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($showsToCompare, 'json'));
    }

    /**
     * @Route("/ranking/vote/up", name="ranking_vote_up")
     */
    public function voteUp(Request $request): Response
    {
        $showRankingRepository = $this->getDoctrine()->getRepository(ShowRanking::class);
            
        $databaseTableName = $request->request->get('database_table_name');
        $showName = $request->request->get('show_name');

        $show = $showRankingRepository->findOneBy(['show_database_table_name' => $databaseTableName]);

        $entityManager = $this->getDoctrine()->getManager();

        if (!is_object($show))
        {
            $showRanking = new ShowRanking();
            $showRanking->setShowName($showName);
            $showRanking->setShowDatabaseTableName($databaseTableName);
            $showRanking->setVotesUp(1);
            $showRanking->setVotesDown(0);

            $entityManager->persist($showRanking);
        } else {
            $score = $show->getVotesUp();
            $show->setVotesUp($score + 1);

            $entityManager->persist($show);
        }

        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * @Route("/ranking/vote/down", name="ranking_vote_down")
     */
    public function voteDown(Request $request): Response
    {
        $showRankingRepository = $this->getDoctrine()->getRepository(ShowRanking::class);
           
        $databaseTableName = $request->request->get('database_table_name');
        $showName = $request->request->get('show_name');
     
        $show = $showRankingRepository->findOneBy(['show_database_table_name' => $databaseTableName]);

        $entityManager = $this->getDoctrine()->getManager();

        if (!is_object($show)) 
        {  
            $showRanking = new ShowRanking();
            $showRanking->setShowName($showName);
            $showRanking->setShowDatabaseTableName($databaseTableName);
            $showRanking->setVotesUp(0);
            $showRanking->setVotesDown(1);

            $entityManager->persist($showRanking);
        } else {
            $score = $show->getVotesDown();
            $show->setVotesDown($score + 1);

            $entityManager->persist($show);
        }

        $entityManager->flush();

        return new JsonResponse(true);
    }
}
