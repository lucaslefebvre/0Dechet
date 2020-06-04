<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * Method for the homepage to show the 3 lastest and the 3 gradest recipes
     * @Route("/", name="home")
     */
    public function home(RecipeRepository $recipeRepository)
    {
        return $this->render('main/home.html.twig', [
            'bestRecipes'=>$recipeRepository->findBestRecipes(),
            'latestRecipes' => $recipeRepository->findLatestRecipes(),
            'title'=>'Accueil',
        ]);
    }

    /**
     * Method for the contact page
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('main/contact.html.twig', [
            'title'=>'Contact',
        ]);
    }
}
