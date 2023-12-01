<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function show(int $id, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);
        // same as $program = $programRepository->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'programs' => $program,
        ]);
    }

    #[Route('/{programID}/season/{seasonID}', methods: ["GET"], requirements:['programID' => '\d+', 'seasonID' => '\d+'], name: 'season_show' )]
    public function showSeason(int $programID, int $seasonID, ProgramRepository $programRepository, SeasonRepository $seasonRepository)
    {
        $program = $programRepository->find($programID);
        $season = $seasonRepository->find($seasonID);
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program, "episodes" => $episodes]);
    }
}
