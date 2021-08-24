<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowsRepository;
use App\Repository\UserRepository;

class IndexControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfHomepageIsSuccessfull()
    {
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextNotContains('html', 'W ostatnim czasie nie dodano żadnego nowego odcinka');
        $this->assertSelectorTextNotContains('html', 'Ranking jest chwilowo niedostępny');
    }

    public function testIfShowsJsonDataCanBeFetched()
    {
        $crawler = $this->client->request('GET', '/api/shows/fetch');

        $response = $this->client->getResponse();

        $shows = json_decode($response->getContent());

        $showsAmount = count(static::$container->get(ShowsRepository::class)->findAll());

        $this->assertTrue($shows[0]->databaseTableName == "victorious");
        $this->assertTrue($shows[0]->name == "Victoria Znaczy Zwycięstwo");

        $this->assertEquals(count($shows), $showsAmount);
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    public function testIfEpisodesJsonDataCanBeFetched()
    {
        $crawler = $this->client->request('GET', '/api/episodes/fetch/jak');

        $response = $this->client->getResponse();

        $episodes = json_decode($response->getContent());

        $this->assertTrue(isset($episodes[0]->id));
        $this->assertTrue(isset($episodes[0]->title));
        $this->assertTrue(isset($episodes[0]->picture));
        $this->assertTrue(isset($episodes[0]->table_name));
        $this->assertTrue(isset($episodes[0]->name));

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfLoggedUserWillSeeHisWatchingHistory()
    {
        $this->client->loginUser(static::$container->get(UserRepository::class)->findOneBy(['email' => 'user_watching_history@gmail.com']));

        $this->client->request('GET', '/');

        $this->assertSelectorTextContains('html', '#Ostatnio oglądane');
    }

    public function testIfUnloggedUserWillNotSeeHisWatchingHistory()
    {
        $this->client->request('GET', '/');

        $this->assertSelectorTextNotContains('html', '#Ostatnio oglądane');
    }

    public function testIfUserWithoutWatchingHistoryWillNotSeeItsContainer()
    {
        $this->client->loginUser(static::$container->get(UserRepository::class)->findOneBy(['email' => 'no_watching_history@gmail.com']));

        $this->client->request('GET', '/');

        $this->assertSelectorTextNotContains('html', '#Ostatnio oglądane');
    }

    public function testIfUserWatchingHistoryCanBeFetched()
    {
        $this->client->loginUser(static::$container->get(UserRepository::class)->findOneBy(['email' => 'user_watching_history@gmail.com']));

        $crawler = $this->client->request('GET', 'api/user/watching/history/fetch');

        $response = $this->client->getResponse();

        $visits = json_decode($response->getContent());

        $this->assertEquals(count($visits), 2);
        $this->assertEquals($visits[0]->series->database_table_name, 'mr_robot');
        $this->assertEquals($visits[0]->episode_id, 1);
        $this->assertEquals($visits[1]->series->database_table_name, 'the_penguins_of_madagascar');
        $this->assertEquals($visits[1]->episode_id, 1);
    }

    public function testIfWatchingHistoryWillReturnEmptyArrayForUnloggedUser()
    {
        $crawler = $this->client->request('GET', 'api/user/watching/history/fetch');

        $response = $this->client->getResponse();

        $visits = json_decode($response->getContent());

        $this->assertEquals(count($visits), 0);
    }
}