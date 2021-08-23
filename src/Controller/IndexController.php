<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use App\Entity\LatestEpisodes;
use App\Entity\ShowRanking;
use App\Entity\Shows;
use App\Entity\UserWatchingHistory;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $showsRanking = $this->getDoctrine()
            ->getRepository(ShowRanking::class)->getTopShows(15);
        
        $showsLatestEpisodes = $this->getDoctrine()
            ->getRepository(LatestEpisodes::class)->getLatestEpisodesWithFilledData(9);

        $userWatchingHistory = $this->getDoctrine()
            ->getRepository(UserWatchingHistory::class)->findBy(['user' => $this->getUser()], ['date' => 'DESC'], 4);

        return $this->render('index/index.html.twig', [
            'showsRanking' => $showsRanking,
            'showsLatestEpisodes' => $showsLatestEpisodes,
            'userWatchingHistory' => $userWatchingHistory
        ]);
    }

    /**
     * @Route("api/shows/fetch", name="api_shows_fetch")
     */
    public function fetchShows()
    {
        $showsRepository = $this->getDoctrine()->getRepository(Shows::class);

        $shows = $showsRepository->findAll();

        $defaultContext = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'description', 'createdAt', 'updatedAt', 'studio', 'category', 'themes', 'userWatchingHistories']
        ];

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($shows, 'json'));
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

    /**
     * @Route("/api/user/watching/history/fetch", name="api_user_watching_history_fetch")
     */
    public function fetchUserWatchingHistory()
    {
        $userWatchingHistory = $this->getDoctrine()
            ->getRepository(UserWatchingHistory::class)->findBy(['user' => $this->getUser()], ['date' => 'DESC']);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($userWatchingHistory, 'json', ['groups' => 'user_watching_history']));
    }
}
