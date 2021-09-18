<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\ShowsRepository;
use App\Repository\LatestEpisodesRepository;
use App\Service\UploadFileService;

class AdminControllerTest extends WebTestCase
{
    public $client = null;

    private $testCasualUser;

    private $testAdminUser;

    private ShowsRepository $showRepository;

    private LatestEpisodesRepository $latestEpisodesRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->testCasualUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'casual_user@gmail.com']);
        $this->testAdminUser = static::$container->get(UserRepository::class)->findOneBy(['email' => 'admin_user@gmail.com']);
        $this->showRepository = static::$container->get(ShowsRepository::class);
        $this->latestEpisodesRepository = static::$container->get(LatestEpisodesRepository::class);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfAdminPageIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $this->client->request('GET', "/admin");
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfOnlyAdminCanSeeAdminPage()
    {
        $this->client->loginUser($this->testCasualUser);

        $this->client->request('GET', "/admin");

        $this->assertTrue(403 == $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfListShowsPageIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $this->client->request('GET', "/admin/show/list");

        $show = $this->showRepository->findAll()[0];

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html', $show->getName());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowCanBeCreated()
    {
        $this->client->loginUser($this->testAdminUser);

        $crawler = $this->client->request('GET', "/admin/show/create");

        $form = $crawler->selectButton('Add Show')->form();

        $name = 'That test show' . uniqid();
        $originalName = 'that_test_show';
        $description = 'Just a test description';

        $form['show[name]'] = $name;
        $form['show[database_table_name]'] = $originalName;
        $form['show[picture]']->upload('./public/assets/pictures/test_picture.png');
        $form['show[description]'] = $description;

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/admin/show/create");

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        $shows = $this->showRepository->findAll();

        $lastAddedShow = $shows[count($shows) - 1];

        $wasShowDatabaseTableCreated = $this->showRepository->checkIfTableExists($originalName);

        if ($lastAddedShow->getName() == $name) {
            $uploadFileService = new UploadFileService(static::$container->get('slugger'));

            $uploadFileService->deleteFile('./public/assets/pictures/shows/' . $lastAddedShow->getPicture());

            $lastAddedShow = $entityManager->merge($lastAddedShow);

            $entityManager->remove($lastAddedShow);
            $entityManager->flush();  
            
            static::$container->get(ShowsRepository::class)->deleteShowDatabaseTable($originalName);
        }
        
        $this->assertTrue($wasShowDatabaseTableCreated);
        $this->assertTrue($lastAddedShow->getName() == $name);
        $this->assertTrue($lastAddedShow->getDatabaseTableName() == $originalName);
        $this->assertTrue(strpos($lastAddedShow->getPicture(), "test-picture") !== false);
        $this->assertTrue($lastAddedShow->getDescription() == $description);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowEpisodeCanBeCreated()
    {
        $this->client->loginUser($this->testAdminUser);

        $showDatabaseTableName = $this->showRepository->findAll()[0]->getDatabaseTableName();

        $crawler = $this->client->request('GET', "/admin/show/episode/create/${showDatabaseTableName}");

        $form = $crawler->selectButton('Submit')->form();

        $episodeTitle = 'Just a test title';
        $episodeSeason = 5;
        $episodeNumber = 110;
        $episodeDescription = 'Just a test description';

        $form['episode[title]'] = $episodeTitle;
        $form['episode[season]'] = $episodeSeason;
        $form['episode[episode_number]'] = $episodeNumber;
        $form['episode[description]'] = $episodeDescription;

        $crawler = $this->client->submit($form);
    
        $this->assertResponseRedirects("/admin/show/episode/create/${showDatabaseTableName}");

        $lastAddedEpisode = $this->showRepository->findLastAddedShowEpisode($showDatabaseTableName);

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        if ($lastAddedEpisode['title'] == $episodeTitle) {
            $this->showRepository->deleteShowEpisode($showDatabaseTableName, $lastAddedEpisode['id']);

            $latestEpisode = $this->latestEpisodesRepository->findOneBy(['show_database_table_name' => $showDatabaseTableName, 'episode_id' => $lastAddedEpisode['id']]);

            $latestEpisode = $entityManager->merge($latestEpisode);

            $entityManager->remove($latestEpisode);
            $entityManager->flush();
        }

        $this->assertTrue($lastAddedEpisode['title'] == $episodeTitle);
        $this->assertTrue($lastAddedEpisode['season'] == $episodeSeason);
        $this->assertTrue($lastAddedEpisode['episode'] == $episodeNumber);
        $this->assertTrue($lastAddedEpisode['description'] == $episodeDescription);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfShowEpisodeCanBeEdited()
    {
        $this->client->loginUser($this->testAdminUser);

        $showDatabaseTableName = $this->showRepository->findAll()[0]->getDatabaseTableName();

        $this->showRepository->saveShowEpisode($showDatabaseTableName, ['title' => 'Just some test title', 'season' => 5, 'episode_number' => 100, 'description' => 'Some description', 'user_id' => 1]);

        $lastAddedEpisode = $this->showRepository->findLastAddedShowEpisode($showDatabaseTableName);

        $crawler = $this->client->request('GET', "/admin/show/episode/edit/{$showDatabaseTableName}/{$lastAddedEpisode['id']}");
    
        $form = $crawler->selectButton('Submit')->form();

        $episodeTitle = 'Just edited test title';
        $episodeSeason = 723;
        $episodeNumber = 873;
        $episodeDescription = 'Just edited test description';

        $form['episode[title]'] = $episodeTitle;
        $form['episode[season]'] = $episodeSeason;
        $form['episode[episode_number]'] = $episodeNumber;
        $form['episode[description]'] = $episodeDescription;

        $crawler = $this->client->submit($form);

        $lastAddedEpisode = $this->showRepository->findLastAddedShowEpisode($showDatabaseTableName);

        if ($lastAddedEpisode['title'] == $episodeTitle) {
            $this->showRepository->deleteShowEpisode($showDatabaseTableName, $lastAddedEpisode['id']);
        }

        $this->assertTrue($lastAddedEpisode['title'] == $episodeTitle);
        $this->assertTrue($lastAddedEpisode['season'] == $episodeSeason);
        $this->assertTrue($lastAddedEpisode['episode'] == $episodeNumber);
        $this->assertTrue($lastAddedEpisode['description'] == $episodeDescription);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfPageWithFilteredVisitorsIsSuccessfull()
    {
        $this->client->loginUser($this->testAdminUser);

        $this->client->request('GET', "/admin/visitors/page/filtered");

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfFileCanBeUploaded()
    {
        $this->client->loginUser($this->testAdminUser);

        $crawler = $this->client->request('GET', '/admin');

        $form = $crawler->selectButton('Upload')->form();

        $form['path'] = 'shows/';
        $form['file']->upload('./public/assets/pictures/test_picture.png');

        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects("/admin");

        $crawler = $this->client->request('GET', '/admin');

        $this->assertSelectorTextContains('html', 'File has been uploaded');

        $uploadFileService = new UploadFileService(static::$container->get('slugger'));

        $uploadFileService->deleteFile('./public/assets/pictures/shows/test-picture.jpg');
    }
}