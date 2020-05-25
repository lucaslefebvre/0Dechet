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
     *  Method to display the recipes by Categories in the template category.html.twig from the directory recipe
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
     * Method to display the recipes by Sub Categories in the template subCategory.html.twig from the directory recipe
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
     * Method to display the recipes by Types in the template type.html.twig from the directory recipe
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
