<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 0; $i < 5000; $i++) {
        $episode = new Episode();
        $episode->setTitle($faker->sentence());
        $episode->setNumber(1);
        $episode->setSeason($this->getReference('season_' . rand(0, 499)));
        $episode->setSynopsis($faker->paragraphs(3, true));
        $this->addReference('episode_' . $i, $episode);

        $manager->persist($episode);
        }

        $manager->flush();        
        
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
            ProgramFixtures::class,
            SeasonFixtures::class
        ];
    }
}