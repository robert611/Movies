<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Studio;

class StudioFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $studios = $this->getStudios();

        foreach ($studios as $key => $data) {
            $studio = new Studio();

            $studio->setName($data['name']);
          
            $manager->persist($studio);

            $this->addReference('studio.' . $key, $studio);
        }

        $manager->flush();
    }

    public function getStudios(): array
    {
        return [
            ['name' => 'usa_network'],
            ['name' => 'abc'],
            ['name' => 'netflix']
        ];
    }
}
