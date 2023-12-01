<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
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