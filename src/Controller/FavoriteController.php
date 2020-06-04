<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    /**
     * Method to add a favorite recipe in his list
     * @Route("/favorite/{slug}", name="favorite")
     */
    public function favorite(Recipe $recipe, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        //We need to have the entity manager to have all of the favorites
        $favoriteRepository = $entityManagerInterface->getRepository(Favorite::class)->findAll();
        //Referer is to prepare the next redirect route to the last page
        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);

        // After the first like it will be always full but to prepare the first time 
        //we need to to that
        if(!empty($favoriteRepository)){

            foreach ($favoriteRepository as $favoriteRecipe) {
                //To know if the user has already this recipe in his favorite list
                if ($favoriteRecipe->getUsers() == $this->getUser() && $favoriteRecipe->getRecipes() == $recipe) {
                    //The user has already this recipe so he clicked on to remove it
                    $entityManagerInterface->remove($favoriteRecipe);
                    $entityManagerInterface->flush();

                    return $this->redirect($referer);
                }
            }   
            //If the foreach with the condition didn't find a correspondence between the user and the recipe,
            // the user want to add this recipe to his list
            $favorite =  new Favorite;
            $favorite->setRecipes($recipe);
            $favorite->setUsers($this->getUser());
    
            $entityManagerInterface->persist($favorite);
            $entityManagerInterface->flush();
    
            return $this->redirect($referer);
            
        }else{
            $favorite =  new Favorite;
            $favorite->setRecipes($recipe);
            $favorite->setUsers($this->getUser());
    
            $entityManagerInterface->persist($favorite);
            $entityManagerInterface->flush();
    
            return $this->redirect($referer);
        }
    }
}