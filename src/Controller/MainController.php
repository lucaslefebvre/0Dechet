<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * Method for the home page to show the 3 lastest and the 3 gradest recipes
     * @Route("/", name="home")
     */
    public function home(RecipeRepository $recipeRepository, Request $request)
    {
        return $this->render('main/home.html.twig', [
            'bestRecipes'=>$recipeRepository->findBestRecipes(),
            'latestRecipes' => $recipeRepository->findLatestRecipes(),
        ]);
    }
}
