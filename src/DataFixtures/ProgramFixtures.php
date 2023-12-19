<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
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
        $program->setTitle($faker->sentence());
        $program->setCategory($this->getReference('category_' . rand(0, 14)));
        $program->setSynopsis($faker->paragraphs(3, true));
        $program->addActor($this->getReference('actor_' . rand(0,9)));
        $slug = $this->slugger->slug($program->getTitle());
        $program->setSlug($slug);
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
