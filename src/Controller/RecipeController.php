<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Rate;
use App\Entity\Recipe;
use App\Entity\SubCategory;
use App\Entity\Type;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RateRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Services\Slugger;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * Method to display all the recipes in template/recipe/browse.html.twig
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(RecipeRepository $recipeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $recipes = $paginator->paginate(  // add paginator
            $recipeRepository->findAll(),   // query to display all the recipes
            $request->query->getInt('page', 1), // number of the current page in the Url, if only one = 1
            1,    // number of results in a page
        ); 

        // If number of pagination exist we return the view
        if (!empty($recipes->getItems())) {
        return $this->render('recipe/browse.html.twig', [
            'recipes' => $recipes,
            'title' => 'Toutes les recettes',
        ]);
          
        } else { // if number of pagination does not exist in URL we throw a 404
            throw $this->createNotFoundException('Pas de recette'); 
        }    
      
    }

    /**
     * Method to display results for research done in the nav search bar
     * Submit the search bar form will redirect on this route
     * @Route("/recherche", name="search", methods={"GET"})
     */
    public function search(RecipeRepository $recipeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        
        //We recuperate the data send in the url by the search form
        $q = $request->query->get('search');
        //Then put it in our customQuery
        $recipes = $paginator->paginate(   // add paginator
                $recipeRepository->findAllWithSearch($q),  // query to display all the recipes of the search results
                $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
                15, // number of results in a page
        );

        // If number of pagination exist we return the view
        if (!empty($recipes->getItems())) {
        return $this->render('recipe/search.html.twig', [
            'recipes' => $recipes,
            'title' => 'Résultat pour '.$q,
        ]);
        } else { // if number of pagination does not exist in URL we throw a 404
            throw $this->createNotFoundException('Pas de recette'); 
        }    
       
    }

     /**
     * Method for add a new recipe. Send a form, receive the response and flush to the Database
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/ajout", name="add", methods={"GET","POST"})
     */
    public function add(Request $request)
    {
        $recipe = new Recipe;

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setStatus(1);
            $recipe->setCreatedAt(new \DateTime());
            $recipe->setSlug('test');
            $recipe->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre recette a été ajoutée'
            );


            return $this->redirectToRoute('recipe_add');
        }

        return $this->render('recipe/add.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'title' => "Ajouter une recette",
        ]);
    }

        /**
     * Method to edit an existing recipe. Send a form, receive the response and flush to the Database
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/edition/{slug}", name="edit", methods={"GET","POST"})
     */
    public function edit(Recipe $recipe, Request $request)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recipe->setUpdatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('user_read', [
                'id' => $this->getUser()->getId(),
            ]);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'title' => "Modifier une recette",
        ]);
    }
  
    /**
     *  Method to display all information about a recipe in template/recipe/show.html.twig
     * @Route("/{slug}", name="show", methods={"GET", "POST"})
     */
    public function show(Recipe $recipe, RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        // Comment Form
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);
        

        if ($_POST) {
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                // Recipe linked to the comment
                $comment->setRecipe($recipe);

                $comment->setStatus(1);
                $comment->setCreatedAt(new \DateTime());
          
                $comment->setUser($this->getUser());

                $em = $this->getDoctrine()->getManager();
                // Cette fois on persiste le genre car c'est un nouvel objet
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('recipe_show', [
                'slug' => $recipe->getSlug(),
                ]);
            }
      
            //Homemade RateForm
            else {
                $rate = new Rate();
                $rating = $_POST['difficulty'];
                $rate->setRate($rating);
                $rate->setRecipe($recipe);
                $rate->setUser($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($rate);
                $entityManager->flush();
                return $this->redirectToRoute('recipe_show', [
                'slug' => $recipe->getSlug(),
                ]);
            }
        }

            return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'title' => $recipe->getName(),
            'commentForm' => $commentForm->createView(),
        ]);

    }
        
     /**
     *  Method to display the recipes by Categories in the template category.html.twig from the directory recipe
     * @Route("/categorie/{slug}", name="browseByCategory")
     */
    public function browseByCategory(Category $category, RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class)->findBy([],['createdAt' => 'desc']);

        $recipes = $paginator->paginate(   // add paginator
            $categoryRepository,  // query to display all the recipes by category
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            15, // number of results in a page
        );

        // If number of pagination exist we return the view
        if (!empty($recipes->getItems())) {
            return $this->render('recipe/category.html.twig', [
                'recipes' => $recipes,
                'category' => $category,
                'title' => $category->getName(),
            ]);
        } else { // if number of pagination does not exist in URL we throw a 404
            throw $this->createNotFoundException('Pas de recette'); 
        }    
    }
          
     /**
     * Method to display the recipes by Sub Categories in the template subCategory.html.twig from the directory recipe
     * @Route("/sous-categorie/{slug}", name="browseBySubCategory")
     */
    public function browseBySubCategory(SubCategory $subCategory,RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request)
    {
        $subCategoryRepository = $this->getDoctrine()->getRepository(SubCategory::class)->findBy([],['createdAt' => 'desc']);

        $recipes = $paginator->paginate(   // add paginator
            $subCategoryRepository,  // query to display all the recipes by subCategory
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            15, // number of results in a page
        );

        // If number of pagination exist we return the view
        if (!empty($recipes->getItems())) {
        return $this->render('recipe/subCategory.html.twig', [
            'recipes' => $recipes,
            'subCategory' => $subCategory,
            'title' => $subCategory->getName(),
        ]);
        } else { // if number of pagination does not exist in URL we throw a 404
            throw $this->createNotFoundException('Pas de recette'); 
        }   
        
    }

    /**
     * Method to display the recipes by Types in the template type.html.twig from the directory recipe
     * @Route("/type/{slug}", name="browseByType")
     */
    public function browseByType(Type $type, RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request)
    {

        $typeRepository = $this->getDoctrine()->getRepository(SubCategory::class)->findBy([],['createdAt' => 'desc']);


        $recipes = $paginator->paginate(   // add paginator
            $typeRepository,  // query to display all the recipes by type
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            15, // number of results in a page
        );

        // If number of pagination exist we return the view
        if (!empty($recipes->getItems())) {
        return $this->render('recipe/type.html.twig', [
            'recipes' => $recipes,
            'type' => $type,
            'title' => $type->getName(),
        ]);
        } else { // if number of pagination does not exist in URL we throw a 404
            throw $this->createNotFoundException('Pas de recette'); 
        }   
            
    }

}
