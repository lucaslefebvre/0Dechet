<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class GlobalCategoriesVariables implements EventSubscriberInterface {

    /**
     * @var \Twig\Environment
     */
    private $twig;
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $manager;

    public function __construct( Environment $twig, EntityManagerInterface $manager ) {
        $this->twig    = $twig;
        $this->manager = $manager;
    }

    // this method is to inject a global variable of Category for Nav and left nav
    public function injectGlobalVariables() {
        $categoryGlobal = $this->manager->getRepository(Category::class)->findAll();
        $this->twig->addGlobal( 'allCategory', $categoryGlobal);

    }

    public static function getSubscribedEvents() {
        return [ KernelEvents::CONTROLLER =>  'injectGlobalVariables' ];
    }
}