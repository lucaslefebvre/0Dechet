<?php

namespace App\EventListener;

use App\Entity\Rate;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AverageRateCalcul
{
    // Ce listener se verra éxécuté la méthod postPersist lors d'un événement postPersist d'un formulaire 
    public function prePersist(LifecycleEventArgs $args)
    {
        //$args contient l'objet concerné par l'événement, c'est à dire n'importe quel objet d'une entite de notre projet
        // Si il est modifié et qu'il y a flush, il peut être intercepté ici.
        $entity = $args->getObject();

        // Si l'objet concerne un ajout de note sur une recette
        if( $entity instanceof Rate ){

            //Récupération de l'objet Recipe en question
            $recipe = $entity->getRecipe();

            //dd(count($recipe->getRates()));

            // Récupération de toutes les notes sur cette recette
            $allRates = $recipe->getRates();

            $addRate = 0;

            // Boucle pour additionner toutes notes
            foreach($allRates as $rate){
                $addRate += $rate->getRate();
            }

            // On set la note moyenne du calcul suivant
            //round(addition des notes / addition du nombre de notes,1, PHP_ROUND_HALF_UP) l'addition des notes précédentes et de la nouvelle note du formulaire / 
            //diviser par le nombre de note +1 (celle ajouté dans ce formulaire)
            $recipe->setAverageRate(round(($addRate + $entity->getRate()) / (count($recipe->getRates())+ 1), 1, PHP_ROUND_HALF_UP));

        }
    }
}