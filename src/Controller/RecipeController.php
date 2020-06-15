<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Rate;
use App\Entity\Recipe;
use App\Entity\SubCategory;
use App\Entity\Type;
use App\Form\CommentType;
use App\Form\DeleteType;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Services\EmbedVideo;
use App\Services\FileUploader;
use App\Services\NumberToAlpha;
use App\Services\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

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
        //We recuperate the data send in the url by the sorting form
        $sortBy = $request->query->get('sortBy');

        $recipes = $paginator->paginate(  // add paginator
        $recipeRepository->findAllRecipes($sortBy),   // query to display all the recipes
        $request->query->getInt('page', 1), // number of the current page in the Url, if only one = 1
        10,    // number of results in a page
        );

        // If number of pagination does not exist in URL we throw a 404
        if (empty($recipes->getItems()) && $recipes->getCurrentPageNumber() !== 1) {
            throw $this->createNotFoundException('Pas de recette'); 
        } else {   // If number of pagination exist we return the view
            return $this->render('recipe/browse.html.twig', [
                'recipes' => $recipes,
                'title' => 'Toutes les recettes',
            ]);
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

        //We recuperate the data send in the url by the sorting form
        $sortBy = $request->query->get('sortBy');

        //Then put it in our customQuery
        $recipes = $paginator->paginate(   // add paginator
                $recipeRepository->findAllWithSearch($q, $sortBy),  // query to display all the recipes of the search results
                $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
                10, // number of results in a page
        );

        // If number of pagination does not exist in URL we throw a 404
        if (empty($recipes->getItems()) && $recipes->getCurrentPageNumber() !== 1) {
            throw $this->createNotFoundException('Pas de recette'); 
        } else {   // If number of pagination exist we return the view
            return $this->render('recipe/search.html.twig', [
                'recipes' => $recipes,
                'title' => 'Résultat pour "'.$q .'"',
            ]);
        }
    }

     /**
     * Method for add a new recipe. Send a form, receive the response and flush to the Database
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/ajout", name="add", methods={"GET","POST"})
     */
    public function add(Request $request, MailerInterface $mailer, FileUploader $fileUploader, EmbedVideo $embedVideo, Slugger $slugger)
    {
        $recipe = new Recipe;

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($recipe);

            $recipe->setStatus(1);
            $recipe->setUser($this->getUser());
            $videoFrame = $embedVideo->videoPlayer($form['video']->getData());
            $recipe->setVideo($videoFrame);

            // We use a Services to move and rename the file
            $newName = $fileUploader->saveFile($form['image'], 'assets/images/recipes');
            $recipe->setImage($newName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);
            $entityManager->flush();
            $entityManager->refresh($recipe);
            //dd($recipe);

            // We create a templated email for confirmation 
            $email = (new TemplatedEmail())
            ->from('equipe0dechet@gmail.com')
            ->to($recipe->getUser()->getEmail())
            ->subject('0\'Déchet - Votre recette a bien été ajoutée')
            ->htmlTemplate('email/recipe/add.html.twig')
            ->context([
                'username' => $recipe->getUser()->getUsername(),
                'name' => $recipe->getName(),
            ]);
    
            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre recette a été ajoutée, un mail de confirmation vous a été envoyé.'
            );

            return $this->redirectToRoute('recipe_show', [
                'slug' => $recipe->getSlug(),
                ]);
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
    public function edit(Recipe $recipe, MailerInterface $mailer, Request $request, FileUploader $fileUploader, EmbedVideo $embedVideo, Slugger $slugger)
    {
        $this->denyAccessUnlessGranted('EDIT', $recipe);

        $image = $recipe->getImage();
        $video = $recipe->getVideo();
        
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // We use a Services to move and rename the file
            $newName = $fileUploader->saveFile($form['image'], 'assets/images/recipes');
            $recipe->setImage($newName);
            // If user don't edit the image we let the old image
            if ($recipe->getImage() === null){
                $recipe->setImage($image);
            }

            $videoFrame = $embedVideo->videoPlayer($form['video']->getData());
            $recipe->setVideo($videoFrame);

            if ($recipe->getVideo() === null){
                $recipe->setVideo($video);
            }

            $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());
            $recipe->setSlug($newSlug);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $entityManager->refresh($recipe);

            // We create a templated email for confirmation 
            $email = (new TemplatedEmail())
            ->from('equipe0dechet@gmail.com')
            ->to($recipe->getUser()->getEmail())
            ->subject('0\'Déchet - Votre recette a bien été modifiée')
            ->htmlTemplate('email/recipe/edit.html.twig')
            ->context([
                'username' => $recipe->getUser()->getUsername(),
                'name' => $recipe->getName(),
            ]);

            $mailer->send($email);


            $this->addFlash(
                'success',
                'Votre recette a été modifiée.'
            );

            return $this->redirectToRoute('recipe_show', [
                'slug' => $recipe->getSlug(),
            ]);
        }

        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('recipe_delete', ['id' => $recipe->getId()])
        ]);

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'deleteForm' => $formDelete->createView(),
            'title' => "Modifier une recette",
        ]);
    }
  
    /**
     *  Method to display all information about a recipe in template/recipe/show.html.twig
     * @Route("/{slug}", name="show", methods={"GET", "POST"})
     */
  
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {

        // Comment Form
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($_POST) {

            if ($commentForm->isSubmitted()) {

                if ($commentForm->isValid()) {
                    // Recipe linked to the comment
                    $comment->setRecipe($recipe);

                    $comment->setStatus(1);
                    $comment->setUser($this->getUser());

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($comment);
                    $em->flush();

                    $this->addFlash(
                        'success',
                        'Votre commentaire a été ajouté.'
                    );

                    return $this->redirectToRoute('recipe_show', [
                    'slug' => $recipe->getSlug(),
                    ]);

                }else{

                    return $this->render('recipe/show.html.twig', [
                        'recipe' => $recipe,
                        'title' => $recipe->getName(),
                        'commentForm' => $commentForm->createView(),
                    ]);
                }
            }
      
            //Homemadeadmin/?entity=User&action=list&menuIndex=6&submenuIndex=-1 RateForm
            else{
                $rate = new Rate();
                $rating = $_POST['difficulty'];

                if($rating <1 || $rating > 5){
                    $this->addFlash(
                        'failed',
                        'La note n\'a pas pu être prise en compte'
                    );

                }else{
                    $rate->setRate($rating);
                    $rate->setRecipe($recipe);
                    $rate->setUser($this->getUser());

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($rate);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Votre note a été prise en compte.'
                    );

                    return $this->redirectToRoute('recipe_show', [
                    'slug' => $recipe->getSlug(),
                    ]);
                }
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
    public function browseByCategory(Category $category, PaginatorInterface $paginator, Request $request, RecipeRepository $recipeRepository)
    {

        //We recuperate the data send in the url by the sorting form
        $sortBy = $request->query->get('sortBy');

        $recipes = $paginator->paginate(   // add paginator
            $recipeRepository->findByCategory($category->getId(), $sortBy),   // query to display all the recipes by category
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            10, // number of results in a page
        );

        // If number of pagination does not exist in URL we throw a 404
        if (empty($recipes->getItems()) && $recipes->getCurrentPageNumber() !== 1) {
            throw $this->createNotFoundException('Pas de recette'); 
        } else {   // If number of pagination exist we return the view
            return $this->render('recipe/category.html.twig', [
                'recipes' => $recipes,
                'category' => $category,
                'title' => $category->getName(),
                ]);
        }
    }
          
     /**
     * Method to display the recipes by Sub Categories in the template subCategory.html.twig from the directory recipe
     * @Route("/sous-categorie/{slug}", name="browseBySubCategory")
     */
    public function browseBySubCategory(SubCategory $subCategory, PaginatorInterface $paginator, Request $request, RecipeRepository $recipeRepository)
    {
        //We recuperate the data send in the url by the sorting form
        $sortBy = $request->query->get('sortBy');

        $recipes = $paginator->paginate(   // add paginator
            $recipeRepository->findBySubCategory($subCategory->getId(), $sortBy),   // query to display all the recipes by category
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            10, // number of results in a page
        );

        // If number of pagination does not exist in URL we throw a 404
        if (empty($recipes->getItems()) && $recipes->getCurrentPageNumber() !== 1) {
            throw $this->createNotFoundException('Pas de recette'); 
        } else {   // If number of pagination exist we return the view
            return $this->render('recipe/subCategory.html.twig', [
                'recipes' => $recipes,
                'subCategory' => $subCategory,
                'title' => $subCategory->getName(),
                ]);
        }
    }

    /**
     * Method to display the recipes by Types in the template type.html.twig from the directory recipe
     * @Route("/type/{slug}", name="browseByType")
     */
    public function browseByType(Type $type, RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request)
    {
        //We recuperate the data send in the url by the sorting form
        $sortBy = $request->query->get('sortBy');

        $recipes = $paginator->paginate(   // add paginator
            $recipeRepository->findByType($type->getId(), $sortBy),   // query to display all the recipes by category
            $request->query->getInt('page', 1),   // number of the current page in the Url, if only one = 1
            10, // number of results in a page
        );

        // If number of pagination does not exist in URL we throw a 404
        if (empty($recipes->getItems()) && $recipes->getCurrentPageNumber() !== 1) {
            throw $this->createNotFoundException('Pas de recette'); 
        } else {   // If number of pagination exist we return the view
            return $this->render('recipe/type.html.twig', [
                'recipes' => $recipes,
                'type' => $type,
                'title' => $type->getName(),
                ]);
        }
    }

    /**
     * Method to allow a user to delete one of his/her recipe off the website
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/suppression/{id}", name="delete", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function delete(EntityManagerInterface $em, MailerInterface $mailer, Request $request, Recipe $recipe)
    {
        $this->denyAccessUnlessGranted('DELETE', $recipe);

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            
            // We create a templated email for confirmation 
            $email = (new TemplatedEmail())
             ->from('equipe0dechet@gmail.com')
             ->to($recipe->getUser()->getEmail())
             ->subject('0\'Déchet - Votre recette a bien été supprimée')
             ->htmlTemplate('email/recipe/delete.html.twig')
             ->context([
                 'username' => $recipe->getUser()->getUsername(),
                 'name' => $recipe->getName(),
             ]);
 
            $mailer->send($email);

            $em->remove($recipe);
            $em->flush();

            $this->addFlash(
                'success',
                'La recette a bien été supprimée.'
            );

            return $this->redirectToRoute('user_read', [
                'slug' => $this->getUser()->getSlug(),
            ]);
        }

        return $this->redirectToRoute('recipe_edit', [
            'slug' => $recipe->getslug(),
        ]);
    }

}
