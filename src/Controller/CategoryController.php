<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository) 
    {
        $category = $categoryRepository->findAll();

        return $this->render(
            'category/index.html.twig',
            ['categories' => $category]
        );
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
        // Deal with the submitted data
        // For example : persiste & flush the entity
            $entityManager->persist($category);
            $entityManager->flush(); 
        // And redirect to a route that display the result
            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', [
            'form' => $form,
            ]);

    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository)
    {
        
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                'No program with name : ' . $categoryName . ' found in category\'s table.'
            );
        } else {
            $programList = $programRepository->findBy(
                ['category' => $category->getID()],
                ['id' => 'DESC'],
                3,
                0
            );
        }

        return $this->render(
            'category/show.html.twig',
            ['categoriesList' => $categoryName, 
            'programs' => $programList]
        );

    }

    
}