<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
    public function show(Program $program): Response
    {
        
        // same as $program = $programRepository->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'programs' => $program,
        ]);
    }

    #[Route('/{program_id}/season/{season_id}', methods: ["GET"], requirements:['program_id' => '\d+', 'season_id' => '\d+'], name: 'season_show' )]
    public function showSeason(#[MapEntity(mapping: ['program_id' => 'id'])] Program $program, #[MapEntity(mapping: ['season_id' => 'id'])] Season $season)
    {
        
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program, "episodes" => $episodes]);
    }

    #[Route('/program/{programId}/season/{seasonId}/episode/{episodeId}', methods: ["GET"], requirements:['programId' => '\d+', 'seasonId' => '\d+', 'episodeId' => '\d+'], name:"episode_show")]
    public function showEpisode(#[MapEntity(mapping: ['programId' => 'id'])] Program $program, #[MapEntity(mapping: ['seasonId' => 'id'])] Season $season, #[MapEntity(mapping: ['episodeId' => 'id'])]Episode $episode)
    {
        return $this->render('program/episode_show.html.twig', ['season' => $season, "program" => $program, "episodes" => $episode]);
    }
}
