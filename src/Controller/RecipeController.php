<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\SubCategory;
use App\Entity\Recipe;
use App\Entity\Type;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     *  Method to display all information about a recipe in template/recipe/show.html.twig
     * @Route("/{slug}", name="show", methods={"GET", "POST"})
     */
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $em, UserRepository $user): Response
    {
        // Comment Form
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // Recipe linked to the comment
            $comment->setRecipe($recipe);

            $comment->setStatus(1);
            $comment->setCreatedAt(new \DateTime());

            // TODO Modify ths part in order to set the user who is connected
            $comment->setUser($user->find(1));

            $em = $this->getDoctrine()->getManager();
            // Cette fois on persiste le genre car c'est un nouvel objet
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('recipe_show', [
                'slug' => $recipe->getSlug(),
            ]);        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'title' => $recipe->getName(),
            'commentForm' => $commentForm->createView(),
        ]);
    }


    /**
     *TODO
     * @Route("/ajout", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setIngredient(['Pomme', 'Banane', 'Sel']);
            $recipe->setEquipement(['Balance']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_browse');
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
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
