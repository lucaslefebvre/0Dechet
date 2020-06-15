<?php

namespace App\EventListener;

use App\Entity\Rate;
use App\Entity\Recipe;
use App\Entity\User;
use App\Services\Slugger;


use Doctrine\Persistence\Event\LifecycleEventArgs;

class SlugifyEvent
{
    // This method is executed prePersist of forms
    public function prePersist(LifecycleEventArgs $args)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        
        $entity = $args->getObject();

        $slugger = new Slugger;

        //If it's a Recipe Object
        if($entity instanceof Recipe){

            $recipe = $entity;
            //Create a new slug for the entity
            $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());

            //Associate the new slug to the entity
            $recipe->setSlug($newSlug);
            return;
        }
        //If it's a Rate Object
        if($entity instanceof Rate){
            $recipe = $entity->getRecipe();

            //Create a new slug for the entity
            $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());

            //Associate the new slug to the entity
            $recipe->setSlug($newSlug);
            return;
        }
        //If it's an User Object
        if($entity instanceof User){

            $user = $entity;
            //Create a new slug for the user
            $newSlug = strtolower($user->getUsername());

            //Associate the new slug to the user
            $user->setSlug($newSlug);
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
    public function postUpdate(LifecycleEventArgs $args)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        $entity = $args->getObject();
 
        $slugger = new Slugger;

         //If it's a Recipe Object
        if($entity instanceof Recipe){

             $recipe = $entity;
             //Create a new slug for the entity
             $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());
 
             //Associate the new slug to the entity
             $recipe->setSlug($newSlug);
        }
         //If it's an User Object
         elseif($entity instanceof User){
             
            $user = $entity;
            //Create a new slug for the user
            $newSlug = strtolower($user->getUsername());

            //Associate the new slug to the user
            $user->setSlug($newSlug);
        }
         //If it's an entity with a slug property other than Recipe
         elseif(property_exists($entity, 'slug')){

             //Create a new slug for the entity
             $newSlug = $slugger->slugify($entity->getName());
 
             //Associate the new slug to the entity
             $entity->setSlug($newSlug);
        }
    }
    // This method is executed postPersist of forms
    public function postPersist(LifecycleEventArgs $args)
    {
        //$args is the object concerned by the evenement
        // if it's modified and flushed, it's intercepted there
        
        $entity = $args->getObject();
        $slugger = new Slugger;

        //If it's a Recipe Object
        if ($entity instanceof Recipe) {
            $recipe = $entity;
            //Create a new slug for the entity
            $newSlug = $slugger->slugify($recipe->getName(), $recipe->getId());

            //Associate the new slug to the entity
            $recipe->setSlug($newSlug);

            $entityManager = $args->getObjectManager();
            $entityManager->flush();
        }
    }
}