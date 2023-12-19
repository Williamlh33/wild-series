<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\CommentType;
use App\Form\ProgramType;
use Symfony\Component\Mime\Email;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('program/index.html.twig', [
            'programs' => $programRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, MailerInterface $mailer, ProgramRepository $programRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', 'The new program has been created');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/new.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function adminProgram(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/admin_index.html.twig', ['programs' => $programs]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route('/{program}/seasons/{season}', name: 'season_show')]
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $season->getEpisodes(),
        ]);
    }

    #[Route('/{program}/seasons/{season}/episodes/{episode}', methods:['GET', 'POST'], name: 'episode_show')]
    public function showEpisode(
        #[MapEntity(mapping: ['program' => 'slug'])] Program $program,
        #[MapEntity(mapping: ['season' => 'id'])] Season $season,
        #[MapEntity(mapping: ['episode' => 'slug'])] Episode $episode,
        Request $request, 
        EntityManagerInterface $entityManagerInterface,
        CommentRepository $commentRepository
        ): Response
        
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("program_episode_show", [
                "program" => $program->getSlug(), 
                "season" => $season->getID(), 
                "episode" => $episode->getSlug()]);
        }

        $comments = $commentRepository->findBy(["episode" => $episode]);

        return $this->render('program/episode_show.html.twig', [
            'season' => $season, 
            "program" => $program, 
            "episode" => $episode, 
            "formContent" => $formComment,
            "comments" => $comments
        ]);
    }

    #[Route('/admin/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', 'The program has been updated');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
            $this->addFlash('success', 'The program has been deleted');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}
