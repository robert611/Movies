<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowCategoryRepository;
use App\Repository\ShowThemeRepository;
use App\Repository\StudioRepository;
use App\Repository\ShowsRepository;

class MetaDataShowSearchControllerTest extends WebTestCase
{
    public $client = null;

    private $showCategoryRepository;

    private $showThemeRepository;

    private $studioRepository;

    private $showsRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->showCategoryRepository = static::$container->get(ShowCategoryRepository::class);
        $this->showThemeRepository = static::$container->get(ShowThemeRepository::class);
        $this->studioRepository = static::$container->get(StudioRepository::class);
        $this->showsRepository = static::$container->get(ShowsRepository::class);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/meta/data/show/search');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('html', 'Wszystkie filmy i seriale');
    }

    public function testIfSearchMenuContainsMainCategories(): void
    {
        $crawler = $this->client->request('GET', '/meta/data/show/search');

        $this->assertSelectorTextContains('html', 'Categories');
        $this->assertSelectorTextContains('html', 'Themes');
        $this->assertSelectorTextContains('html', 'Networks');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfMenuContainsCategoriesThatAreUsed(): void
    {
        $this->client->request('GET', '/meta/data/show/search');

        $categories = $this->showCategoryRepository->findCategoriesContainingShows();

        foreach ($categories as $category)
        {
            $this->assertSelectorTextContains('html', ucfirst($category['name']));
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfMenuContainsThemesThatAreUsed(): void
    {
        $this->client->request('GET', '/meta/data/show/search');

        $themes = $this->showThemeRepository->findThemesContainingShows();

        foreach ($themes as $theme)
        {
            $this->assertSelectorTextContains('html', ucfirst($theme->getName()));
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfMenuContainsStudiosThatAreUsed(): void
    {
        $this->client->request('GET', '/meta/data/show/search');

        $studios = $this->studioRepository->findStudiosContainingShows();

        foreach ($studios as $studio)
        {
            $this->assertSelectorTextContains('html', ucfirst($studio['name']));
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowsCanBeSearchedByCategory(): void
    {
        $category = $this->showCategoryRepository->findCategoriesContainingShows()[0];

        $this->client->request('GET', '/meta/data/show/search?categoryId=' . $category['id']);

        $foundShows = $this->showsRepository->findShowsByCategory($category['id']);

        $this->assertSelectorTextContains('html', 'Wyniki dla kategorii: ' . ucfirst($category['name']));
        
        foreach ($foundShows as $show)
        {
            $this->assertSelectorTextContains('html', $show->getName());
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowsCanBeSearchedByTheme(): void
    {
        $theme = $this->showThemeRepository->findThemesContainingShows()[0];

        $this->client->request('GET', '/meta/data/show/search?themeId=' . $theme->getId());

        $foundShows = $this->showsRepository->findShowsByTheme($theme);

        $this->assertSelectorTextContains('html', 'Wyniki dla motywu: ' . ucfirst($theme->getName()));
        
        foreach ($foundShows as $show)
        {
            $this->assertSelectorTextContains('html', $show->getName());
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowsCanBeSearchedByStudio(): void
    {
        $studio = $this->studioRepository->findStudiosContainingShows()[0];

        $this->client->request('GET', '/meta/data/show/search?studioId=' . $studio['id']);

        $foundShows = $this->showsRepository->findShowsByStudio($studio['id']);

        $this->assertSelectorTextContains('html', 'Wyniki dla studia: ' . ucfirst($studio['name']));
        
        foreach ($foundShows as $show)
        {
            $this->assertSelectorTextContains('html', $show->getName());
        }
    }
}