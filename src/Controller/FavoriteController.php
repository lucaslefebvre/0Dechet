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
     * @Route("/favorite/{slug}", name="favorite")
     */
    public function favorite(Recipe $recipe, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        /*
        $favoriteRepository = $entityManagerInterface->getRepository(Favorite::class)->findAll();
        dd($favoriteRepository);




        $favorite =  new Favorite;
        $favorite->setRecipes($recipe);
        $favorite->setUsers($this->getUser());

        $entityManagerInterface->persist($favorite);
        $entityManagerInterface->flush();
        */
        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);

        return $this->redirect($referer);
    }
}
