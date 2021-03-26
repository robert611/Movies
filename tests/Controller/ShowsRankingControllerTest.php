<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowRankingRepository;
use App\Repository\ShowsRepository;
use App\Repository\UserRepository;
use App\Entity\Shows;

class ShowsRankingControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowsToCompareCanBeFind()
    {
        $this->client->request('GET', '/api/ranking/find/shows/to/compare');

        $response = $this->client->getResponse();

        $showsToCompare = json_decode($response->getContent());

        $this->assertEquals(count($showsToCompare), 2);
        $this->assertTrue(isset($showsToCompare[0]->id));
        $this->assertTrue(isset($showsToCompare[0]->name));
        $this->assertTrue(isset($showsToCompare[1]->databaseTableName));
        $this->assertTrue(isset($showsToCompare[1]->picture));
        $this->assertNotEquals($showsToCompare[0]->id, $showsToCompare[1]->id);

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfItIsPossibleToVoteForGivenShow()
    {   
        $showDatabaseTableName = 'victorious';

        $votesUp = static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => $showDatabaseTableName])->getVotesUp();

        $this->client->request('POST', '/ranking/vote/up', ['database_table_name' => $showDatabaseTableName]);

        $alteredVotesUp = static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => $showDatabaseTableName])->getVotesUp();

        $this->assertEquals($votesUp + 1, $alteredVotesUp);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfItIsPossibleToVoteAgainstGivenShow()
    {   
        $showDatabaseTableName = 'victorious';

        $votesDown = (int) static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => $showDatabaseTableName])->getVotesDown();

        $this->client->request('POST', '/ranking/vote/down', ['database_table_name' => $showDatabaseTableName]);

        $alteredVotesDown = (int) static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => $showDatabaseTableName])->getVotesDown();
        
        $this->assertTrue(($votesDown + 1) == $alteredVotesDown);
    }

    /**
     * @dataProvider provideUrls
     * @runInSeparateProcess
     */
    public function testIfShowRecordWillBeAddedIfDoesNotAlreadyExist($url)
    {
        $showDatabaseTableName = 'test_database_table_name';

        /* Create New Show */
        $show = new Shows();

        $show->setUser(static::$container->get(UserRepository::class)->findAll()[0]);
        $show->setName('test_show_name');
        $show->setDatabaseTableName($showDatabaseTableName);
        $show->setPicture('test_picture.jpg');
        $show->setDescription('Test description');
        $show->setCreatedAt(new \DateTime(date('Y-m-d')));
        
        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        $entityManager->persist($show);
        $entityManager->flush();

        $this->client->request('POST', $url, ['database_table_name' => $showDatabaseTableName]);

        $showRanking = static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => $showDatabaseTableName]);

        $entityManager->remove($show);
        $entityManager->remove($showRanking);
        $entityManager->flush();

        $this->assertTrue($showRanking->getShowDatabaseTableName() == $showDatabaseTableName);
    }

    /**
     * @dataProvider provideUrls
     * @runInSeparateProcess
     */
    public function testIfUserCannotVoteForNotExistingShow($url)
    {
        $this->client->request('POST', $url, ['database_table_name' => 'not_existing_vote']);

        $showRanking = static::$container->get(ShowRankingRepository::class)->findOneBy(['show_database_table_name' => 'not_existing_vote']);

        $response = $this->client->getResponse();

        $this->assertFalse(json_decode($response->getContent()));
        $this->assertTrue(is_null($showRanking));
    }

    public function provideUrls()
    {
        return [
            ["/ranking/vote/up"],
            ["/ranking/vote/down"]
        ];
    }
}