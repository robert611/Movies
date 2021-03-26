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
use App\Repository\ShowsRepository;
use App\Entity\ShowRanking;

class ShowsRankingController extends AbstractController
{
    private ShowsRepository $showsRepository;

    public function __construct(ShowsRepository $showsRepository)
    {
        $this->showsRepository = $showsRepository;
    }

    /**
     * @Route("/api/ranking/find/shows/to/compare", name="ranking_find_shows_to_compare")
     */
    public function findShowsToCompare(): Response
    {
        $shows = $this->showsRepository->findAll();

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

        $showRanking = $showRankingRepository->findOneBy(['show_database_table_name' => $databaseTableName]);

        $show = $this->showsRepository->findOneBy(['database_table_name' => $databaseTableName]);

        if (!$show) {
            return new JsonResponse(false);
        }

        $entityManager = $this->getDoctrine()->getManager();

        if (!is_object($showRanking))
        {
            $showRanking = new ShowRanking();
            $showRanking->setShowName($show->getName());
            $showRanking->setShowDatabaseTableName($databaseTableName);
            $showRanking->setVotesUp(1);
            $showRanking->setVotesDown(0);

            $entityManager->persist($showRanking);
        } else {
            $score = $showRanking->getVotesUp();
            $showRanking->setVotesUp($score + 1);

            $entityManager->persist($showRanking);
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
     
        $showRanking = $showRankingRepository->findOneBy(['show_database_table_name' => $databaseTableName]);

        $show = $this->showsRepository->findOneBy(['database_table_name' => $databaseTableName]);

        if (!$show) {
            return new JsonResponse(false);
        }

        $entityManager = $this->getDoctrine()->getManager();

        if (!is_object($showRanking)) 
        {  
            $showRanking = new ShowRanking();
            $showRanking->setShowName($show->getName());
            $showRanking->setShowDatabaseTableName($databaseTableName);
            $showRanking->setVotesUp(0);
            $showRanking->setVotesDown(1);

            $entityManager->persist($showRanking);
        } else {
            $score = $showRanking->getVotesDown();
            $showRanking->setVotesDown($score + 1);

            $entityManager->persist($showRanking);
        }

        $entityManager->flush();

        return new JsonResponse(true);
    }
}
