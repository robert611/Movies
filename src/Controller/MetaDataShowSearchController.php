<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ShowsRepository;
use App\Repository\ShowThemeRepository;
use App\Repository\ShowCategoryRepository;
use App\Repository\StudioRepository;

class MetaDataShowSearchController extends AbstractController
{
    #[Route('/meta/data/show/search', name: 'meta_data_show_search')]
    public function index(ShowsRepository $showsRepository, ShowThemeRepository $showThemeRepository, ShowCategoryRepository $showCategoryRepository, StudioRepository $studioRepository, Request $request): Response
    {
        $categories = $showCategoryRepository->findCategoriesContainingShows();
        $themes = $showThemeRepository->findThemesContainingShows();
        $studios = $studioRepository->findStudiosContainingShows();

        $categoryId = (int) $request->query->get('categoryId');
        $themeId = (int) $request->query->get('themeId');
        $studioId = (int) $request->query->get('studioId');

        if ($categoryId)
        {
            $category = $showCategoryRepository->find($categoryId);
            $foundShows = $showsRepository->findShowsByCategory($categoryId);
        }
        else if ($themeId)
        {
            $theme = $showThemeRepository->find($themeId);
            $foundShows = $showsRepository->findShowsByTheme($theme);
        }
        else if ($studioId)
        {
            $studio = $studioRepository->find($studioId);
            $foundShows = $showsRepository->findShowsByStudio($studioId);
        }
        else
        {
            $foundShows = $showsRepository->findAll();
        }

        return $this->render('meta_data_show_search/index.html.twig', [
            'categories' => $categories,
            'themes' => $themes,
            'studios' => $studios,
            'foundShows' => $foundShows,
            'theme' => $theme ?? null,
            'category' => $category ?? null,
            'studio' => $studio ?? null
        ]);
    }
}
