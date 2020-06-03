<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Services\FileUploader;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
    private function setUserPassword(User $user): void
    {
        if ($user->getPassword()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        }
    }

    /**
     * @required
     */
    public function setEncoder(UserPasswordEncoderInterface $encoder): void
    {
        $this->encoder = $encoder;
    }

    public function persistUserEntity(User $user): void
    {
        $this->setUserPassword($user);

        $this->persistEntity($user);
    }

    public function updateUserEntity(User $user): void
    {
        $this->setUserPassword($user);

        $this->updateEntity($user);
    }

}