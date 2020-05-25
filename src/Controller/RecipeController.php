<?php

namespace App\Controller;


use App\Entity\Category;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * @Route("/recipe/{slug}", name="recipe")
     */
    public function browseByCategory(Category $category)

    {

        return $this->render('recipe/category.html.twig', [
            'category' => $category,
        ]);
    }
}
