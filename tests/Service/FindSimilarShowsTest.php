<?php

namespace App\Tests\Model;

use App\Service\FindSimilarShows;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\ShowsRepository;
use App\Repository\UserRepository;
use App\Entity\Shows;
use App\Entity\Studio;
use App\Entity\ShowCategory;
use App\Entity\ShowTheme;

class FindSimilarShowsTest extends WebTestCase
{
    private $findSimilarShows;
    private $showsRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->showsRepository = static::$container->get(ShowsRepository::class);

        $this->findSimilarShows = new FindSimilarShows($this->showsRepository);
    }

    public function testIfNumberOfReturnedShowsCanBeLimited()
    {
        $victorious = $this->showsRepository->findOneBy(['database_table_name' => 'victorious']);

        $limit = 2;

        $similarShows = $this->findSimilarShows->getSimilarShows($victorious, $limit);

        $this->assertTrue(count($similarShows) <= $limit);
    }

    public function testIfSimilarityPointsAreAssignedCorrectly()
    {
        $similarShows = $this->findSimilarShows->evaluateShowsSimilarity($this->getShows()[0], $this->getShows());
    
        $this->assertEquals(count($similarShows), 2);
        $this->assertEquals($similarShows[0]['show']->getName(), 'Mr. Robot');
        $this->assertEquals($similarShows[1]['show']->getName(), 'House Of Cards');
        $this->assertEquals($similarShows[0]['similarity_points'], 25);
        $this->assertEquals($similarShows[1]['similarity_points'], 10);
    }

    public function testIfShowsCanBeSortedBySimilarityPoints()
    {
        $showsWithAssignedPoints = [
            ['show' => 'Burn Notice', 'similarity_points' => 20],
            ['show' => 'Cars', 'similarity_points' => 30],
            ['show' => 'House Of Cards', 'similarity_points' => 10],
            ['show' => 'Lost', 'similarity_points' => 5]
        ];

        $sortedShows = $this->findSimilarShows->sortShowsBySimilarityPoints($showsWithAssignedPoints);

        $this->assertEquals($sortedShows[0]['show'], 'Cars');
        $this->assertEquals($sortedShows[1]['show'], 'Burn Notice');
        $this->assertEquals($sortedShows[2]['show'], 'House Of Cards');
        $this->assertEquals($sortedShows[3]['show'], 'Lost');
    }

    public function getShows(): array
    {
        $user = static::$container->get(UserRepository::class)->findOneBy(['username' => 'casual_user']);

        $usaNetwork = new Studio();
        $usaNetwork->setName('usa_network');

        $theHub = new Studio();
        $theHub->setName('the_hub');

        $disney = new Studio();
        $disney->setName('disney');

        $dramaCategory = new ShowCategory();
        $dramaCategory->setName('drama');

        $politicsCategory = new ShowCategory();
        $politicsCategory->setName('politics');

        $animationCategory = new ShowCategory();
        $animationCategory->setName('animation');

        $sitcomTheme = new ShowTheme();
        $sitcomTheme->setId(1);
        $sitcomTheme->setName('sitcom');

        $hackingTheme = new ShowTheme();
        $hackingTheme->setName('hacking');

        $kidsTheme = new ShowTheme();
        $kidsTheme->setName('kids');

        $burnNotice = new Shows();
        $burnNotice->setId(1);
        $burnNotice->setUser($user);
        $burnNotice->setName('Burn Notice');
        $burnNotice->setDatabaseTableName('burn_notice');
        $burnNotice->setDescription('Description');
        $burnNotice->setCreatedAt(new \DateTime());
        $burnNotice->setUpdatedAt(null);
        $burnNotice->setCategory($dramaCategory);
        $burnNotice->setStudio($usaNetwork);
        $burnNotice->addTheme($sitcomTheme);

        $mrRobot = new Shows();
        $mrRobot->setId(2);
        $mrRobot->setUser($user);
        $mrRobot->setName('Mr. Robot');
        $mrRobot->setDatabaseTableName('mr_robot');
        $mrRobot->setDescription('Description');
        $mrRobot->setCreatedAt(new \DateTime());
        $mrRobot->setUpdatedAt(null);
        $mrRobot->setCategory($dramaCategory);
        $mrRobot->setStudio($usaNetwork);
        $mrRobot->addTheme($hackingTheme);

        $houseOfCards = new Shows();
        $houseOfCards->setId(3);
        $houseOfCards->setUser($user);
        $houseOfCards->setName('House Of Cards');
        $houseOfCards->setDatabaseTableName('house_of_cards');
        $houseOfCards->setDescription('Description');
        $houseOfCards->setCreatedAt(new \DateTime());
        $houseOfCards->setUpdatedAt(null);
        $houseOfCards->setCategory($politicsCategory);
        $houseOfCards->setStudio($theHub);
        $houseOfCards->addTheme($sitcomTheme);

        $cars = new Shows();
        $cars->setId(4);
        $cars->setUser($user);
        $cars->setName('Cars');
        $cars->setDatabaseTableName('cars');
        $cars->setDescription('Description');
        $cars->setCreatedAt(new \DateTime());
        $cars->setUpdatedAt(null);
        $cars->setCategory($animationCategory);
        $cars->setStudio($disney);
        $cars->addTheme($kidsTheme);

        $sitcomTheme->addShow($burnNotice);
        $sitcomTheme->addShow($houseOfCards);

        return [
            $burnNotice,
            $mrRobot,
            $houseOfCards,
            $cars
        ];
    }
}