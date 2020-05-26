<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Rate;
use App\Entity\Recipe;
use App\Entity\SubCategory;
use App\Entity\Type;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
* @Route("/recette", name="recipe_")
*/
class RecipeController extends AbstractController
{
    /**
     *  Method to display all the recipes in template/recipe/browse.html.twig
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/browse.html.twig', [
            'recipes' => $recipeRepository->findAll(),
            'title' => 'Toutes les recettes'
        ]);
    }
 

     /**
     * Method for add a new recipe. Send a form, receive the response and flush to the Database
     * @Route("/ajout", name="new", methods={"GET","POST"})
     */
    public function addRecipe(Request $request)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_new');
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }
  
    /**
     *  Method to display all information about a recipe in template/recipe/show.html.twig
     * @Route("/{slug}", name="show", methods={"GET","POST"})
     */
    public function show(Recipe $recipe, UserInterface $user): Response
    {
        $user = $this->getUser();

        if ($_POST) {
            $rate = new Rate();
            if (isset($_POST['star-5'])) {
                $rating = 5;
            } elseif (isset($_POST['star-4'])) {
                $rating = 4;
            } elseif (isset($_POST['star-3'])) {
                $rating = 3;
            } elseif (isset($_POST['star-2'])) {
                $rating = 2;
            } else {
                $rating = 1;
            }

            $rate->setRate($rating);
            $rate->setRecipe($recipe);
            $rate->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rate);
            $entityManager->flush();
            return $this->redirectToRoute('recipe_browse');
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'title' => $recipe->getName(),
        ]);
    }

         
     /**
     *  Method to display the recipes by Categories in the template category.html.twig from the directory recipe
     * @Route("/categorie/{slug}", name="browseByCategory")
     */
    public function browseByCategory(Category $category)
    {
        return $this->render('recipe/category.html.twig', [
            'category' => $category,
            'title' => $category->getName()
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
            'title' => $subCategory->getName()
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
            'title' => $type->getName()
        ]);
    }

}
