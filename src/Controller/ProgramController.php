<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    #[Route('/program/{page}', methods : ['GET'], requirements: ['page'=>'\d+'], name: 'program_index')]
    public function index(int $page = 1): Response
    {
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series',
            'page' => $page
         ]);
    }
}