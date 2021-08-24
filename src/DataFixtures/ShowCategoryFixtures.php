<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ShowCategory;

class ShowCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $showCategories = $this->getShowCategories();

        foreach ($showCategories as $key => $data) {
            $showCategory = new ShowCategory();

            $showCategory->setName($data['name']);
          
            $manager->persist($showCategory);

            $this->addReference('show_category.' . $key, $showCategory);
        }

        $manager->flush();
    }

    public function getShowCategories(): array
    {
        return [
            ['name' => 'comedy'],
            ['name' => 'sci-fi'],
            ['name' => 'horror'],
            ['name' => 'romance'],
            ['name' => 'action'],
            ['name' => 'thriller'],
            ['name' => 'drama'],
            ['name' => 'mystery'],
            ['name' => 'crime'],
            ['name' => 'animation'],
            ['name' => 'adventure'],
            ['name' => 'fantasy'],
            ['name' => 'comedy-romance'],
            ['name' => 'action-comedy'],
            ['name' => 'superhero'],
            ['name' => 'teenage'],
            ['name' => 'manga'],
            ['name' => 'anime']
        ];
    }
}
