<?php

namespace App\EventListener;

use App\Entity\Recipe;
use App\Services\Slugger;


use Doctrine\Persistence\Event\LifecycleEventArgs;

class SlugifyEvent
{
    // This method is executed prePersist of forms
    public function prePersist(LifecycleEventArgs $args, Slugger $slugger)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        $entity = $args->getObject();

        //If it's a Recipe Object
        if($entity instanceof Recipe){

            $recipe = $entity;
            //Create a new slug for the entity
            $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());

            //Associate the new slug to the entity
            $recipe->setSlug($newSlug);
        }
        //If it's an entity with a slug property other than Recipe
        elseif(property_exists($entity, 'slug')){

            //Create a new slug for the entity
            $newSlug = $slugger->slugify($entity->getName());

            //Associate the new slug to the entity
            $entity->setSlug($newSlug);
        }
    }
    // This method is executed preUpdate of forms
    public function preUpdate(LifecycleEventArgs $args, Slugger $slugger)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        $entity = $args->getObject();
 
         //If it's a Recipe Object
        if($entity instanceof Recipe){
 
             $recipe = $entity;
             //Create a new slug for the entity
             $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());
 
             //Associate the new slug to the entity
             $recipe->setSlug($newSlug);
        }
         //If it's an entity with a slug property other than Recipe
         elseif(property_exists($entity, 'slug')){
 
             //Create a new slug for the entity
             $newSlug = $slugger->slugify($entity->getName());
 
             //Associate the new slug to the entity
             $entity->setSlug($newSlug);
        }
    }
}