<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/recette", name="recipe_")
 */
class RecipeController extends AbstractController
{
    /**
      * @Route("/categorie/{slug}", name="browseByCategory")
      */
    public function browseByCategory(Category $category)
    {
        return $this->render('recipe/category.html.twig', [
            'category' => $category,
            'title' => 'Affichage des recettes selon les catégories'
        ]);
    }

    /**
     * @Route("/sous-categorie/{slug}", name="browseBySubCategory")
     */
    public function browseBySubCategory(SubCategory $subCategory)
    {
        return $this->render('recipe/subCategory.html.twig', [
            'subCategory' => $subCategory,
            'title' => 'Affichage des recettes selon les sous-catégories'
        ]);
    }

    /**
     * @Route("/type/{slug}", name="browseByType")
     */
    public function browseByType(Type $type)
    {
        return $this->render('recipe/type.html.twig', [
            'type' => $type,
            'title' => 'Affichage des recettes selon les types'
        ]);
    }

}
