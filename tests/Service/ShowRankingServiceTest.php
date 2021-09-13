<?php

namespace App\Tests\Service;

use App\Service\ShowRankingService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShowRankingServiceTest extends KernelTestCase
{
    private $showRankingService;

    public function setUp(): void
    {
        $this->showRankingService = new ShowRankingService($this->getRanking());
    }

    public function testGetPosition(): void
    {
        $this->assertEquals($this->showRankingService->getPosition('lost'), 1);
        $this->assertEquals($this->showRankingService->getPosition('burn_notice'), 2);
        $this->assertEquals($this->showRankingService->getPosition('terror'), 3);
        $this->assertEquals($this->showRankingService->getPosition('mr_robot'), 4);
        $this->assertEquals($this->showRankingService->getPosition('transformers'), null);
        $this->assertEquals($this->showRankingService->getPosition('friends'), null);
        $this->assertNotEquals($this->showRankingService->getPosition('mr_robot'), 1);
    }
    
    public function getRanking(): array
    {
        return [
            ['show_database_table_name' => 'lost'],
            ['show_database_table_name' => 'burn_notice'],
            ['show_database_table_name' => 'terror'],
            ['show_database_table_name' => 'mr_robot'],
        ];
    }
}