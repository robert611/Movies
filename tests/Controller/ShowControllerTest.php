<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowLinksRepository;
use App\Repository\ShowsRepository;

class ShowControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowPageIsSuccessfull()
    {
        $this->client->request('GET', '/show/victorious/1');

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
        $this->client->request('GET', '/show/victorious/1');

        $episode = static::$container->get(ShowsRepository::class)->findEpisode('victorious', 1);

        $this->assertSelectorTextContains('html', $episode['description']);
        $this->assertSelectorTextContains('html', $episode['title']);
        $this->assertSelectorTextContains('html', "#Odcinek " . $episode['episode']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCantSeeNotExistingEpisode()
    {
        $this->client->request('GET', '/show/victorious/11111');

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
}