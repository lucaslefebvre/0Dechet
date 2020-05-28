<?php

namespace App\EventListener;

use App\Entity\Rate;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AverageRateCalcul
{
    // This method is executed postPersist of forms
    public function prePersist(LifecycleEventArgs $args)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        $entity = $args->getObject();

        //If it's a Rate Object
        if( $entity instanceof Rate ){

            // Recovery the Recipe Object
            $recipe = $entity->getRecipe();

            // Recovery all rates of this recipe
            $allRates = $recipe->getRates();

            $addRate = 0;

            // Foreach for adding all rates
            foreach($allRates as $rate){
                $addRate += $rate->getRate();
            }

            // We can Set AverageRate in the Recipe object with this calcul
            //round(add all rates / Number of rates,1, PHP_ROUND_HALF_UP) add all rates + new rate /(divid by) 
            //number of rates + 1 (the new one)
            $recipe->setAverageRate(round(($addRate + $entity->getRate()) / (count($recipe->getRates())+ 1), 1, PHP_ROUND_HALF_UP));

        }
    }
}