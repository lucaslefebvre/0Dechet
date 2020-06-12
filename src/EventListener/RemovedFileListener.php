<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManager;
use Vich\UploaderBundle\Event\Event;

class RemovedFileListener
{
    protected $em;
    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // make sure a file entity object is removed after the file is deleted
    public function onPostRemove(Event $event)
    {
        if (isset($_POST['user']['imageFile']['delete']) && $_POST['user']['imageFile']['delete'] == 1){
            $event->getObject()->setImage(null) ;
            $this->em->flush();

        }

        if (isset($_POST['recipe']['imageFile']['delete']) && $_POST['recipe']['imageFile']['delete'] == 1){
            $event->getObject()->setImage(null) ;
            $this->em->flush();

        }
    }
}