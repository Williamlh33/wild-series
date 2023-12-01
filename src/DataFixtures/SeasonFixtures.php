<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $season = new Season();
        $season->setNumber(1);
        $season->setProgram($this->getReference('program_Arcane'));
        $season->setYear('2021');
        $season->setDescription('Le scénario suit principalement Jinx et Vi, deux sœurs ayant vécu une enfance difficile à Zaun, mais qui, désormais adultes, mènent une vie très différente');
        $manager->persist($season);
        $this->addReference('season1_Arcane', $season);
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
            ProgramFixtures::class
        ];
    }
}