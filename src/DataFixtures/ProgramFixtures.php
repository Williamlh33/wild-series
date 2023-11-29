<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAM = [
        "Walking Dead" => [
            "synopsis" => "Des zombies envahissent la terre",
            "category" => "category_Action"
        ],
        "Donjons et Dragons" => [
            "synopsis" => "Un voleur attachant et un groupe d'aventuriers disparates entreprennent un raid risqué pour récupérer une relique perdue.",
            "category" => "category_Aventure"
        ],
        "Ghost in the Shell" => [
            "synopsis" => "En 2029, le monde, ainsi que l'âme humaine, sont contrôlés par Internet. Motoko Kusagani, une cyberpolicière, et Batou, deux cyborgs appartenant à la section 9, anti-terroriste, doivent mettre la main sur un hacker mystérieux en contact avec un diplomate corrompu. ",
            "category" => "category_Animation"
        ],
        "Le Seigneur des anneaux : La Communauté de l'anneau" => [
            "synopsis" => "Un jeune et timide hobbit, Frodon Sacquet, hérite d'un anneau magique. Sous ses apparences de simple bijou, il s'agit en réalité d'un instrument de pouvoir absolu qui permettrait à Sauron, le Seigneur des ténèbres, de régner sur la Terre du Milieu et de réduire en esclavage ses peuples.",
            "category" => "category_Fantastique"
        ],
        "Saw" => [
            "synopsis" => "Dans une salle de bain désaffectée, le photographe Adam Stanheight et le docteur Lawrence Gordon se réveillent, aux coins opposés de la pièce, enchaînés par leurs chevilles à des canalisations.",
            "category" => "category_Horreur"
        ]
    ];

    public function load(ObjectManager $manager): void
    {       

        foreach(self::PROGRAM as $titleSerie => $content) {
        $program = new Program();
        $program->setTitle($titleSerie);
        $program->setSynopsis($content['synopsis']);
        $program->setCategory($this->getReference($content['category']));
        $manager->persist($program);
       
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
