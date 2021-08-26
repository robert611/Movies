<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowLinksRepository;
use App\Repository\ShowsRepository;
use App\Service\FindSimilarShows;

class ShowControllerTest extends WebTestCase
{
    public $client = null;
    private $showsRepository;
    private $show;
    private $findSimilarShows;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->showsRepository = static::$container->get(ShowsRepository::class);
        $this->show = $this->showsRepository->findOneBy([]);
        $this->findSimilarShows = new FindSimilarShows($this->showsRepository);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowPageIsSuccessfull()
    {
        $this->client->request('GET', '/show/' . $this->show->getDatabaseTableName() .'/1');

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfEpisodeLinksAreDisplayed()
    {
        $this->client->request('GET', '/show/suite_life_ship/1');

        $episodeLinks = static::$container->get(ShowLinksRepository::class)->findBy(['show_database_table_name' => 'suite_life_ship', 'episode_id' => 1]);

        foreach ($episodeLinks as $link)
        {
            if (strlen(trim($link->getName())) == 0)
            {
                $this->assertSelectorTextContains('html', "Nieznane źródło");
            } else {
                $this->assertSelectorTextContains('html', $link->getName());
            }
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfEpisodePropertiesAreDisplayed()
    {
        $this->client->request('GET', '/show/' . $this->show->getDatabaseTableName() . '/1');

        $episode = $this->showsRepository->findEpisode('victorious', 1);

        $this->assertSelectorTextContains('html', $episode['description']);
        $this->assertSelectorTextContains('html', $episode['title']);
        $this->assertSelectorTextContains('html', "#Odcinek " . $episode['episode']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCantSeeNotExistingEpisode()
    {
        $this->client->request('GET', '/show/' . $this->show->getDatabaseTableName() . '/11111');

        $this->assertResponseRedirects("/");

        $this->client->request('GET', '/');

        $this->assertSelectorTextContains('html', 'Nie udało się nam znaleźć tego odcinka');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCantSeeNotExistingShow()
    {
        $this->client->request('GET', '/show/not_existing_show_name_test/1');

        $this->assertResponseRedirects("/");

        $this->client->request('GET', '/');

        $this->assertSelectorTextContains('html', 'Nie ma takiego serialu');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCanSeeSimilarShows()
    {
        $this->client->request('GET', '/show/' . $this->show->getDatabaseTableName());

        $similarShows = $this->findSimilarShows->getSimilarShows($this->show, 1);

        $this->assertSelectorTextContains('html', '#Podobne Filmy i Seriale');
        $this->assertSelectorTextContains('html', $similarShows[0]['show']->getName());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowStudioIsDisplayed()
    {
        $this->client->request('GET', '/show/' . $this->show->getDatabaseTableName());

        $studioName = $this->show->getStudio()->getSlug();

        $this->assertSelectorTextContains('html', $studioName);
    }
}