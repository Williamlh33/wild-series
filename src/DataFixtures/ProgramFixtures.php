<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        /*foreach(self::PROGRAM as $titleSerie => $content)
        {
        $program = new Program();
        $program->setTitle($titleSerie);
        $program->setSynopsis($content['synopsis']);
        $program->setCategory($this->getReference($content['category']));
        $manager->persist($program);
       
        }

        $manager->flush();*/

        for($i = 0; $i < 50; $i++) {
        $program = new Program();
        $program->setTitle($faker->title());
        $program->setCategory($this->getReference('category_' . rand(0, 14)));
        $program->setSynopsis($faker->paragraphs(3, true));
        $program->addActor($this->getReference('actor_' . rand(0,9)));
        $manager->persist($program);
        $this->addReference('program_' . $i, $program);

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
