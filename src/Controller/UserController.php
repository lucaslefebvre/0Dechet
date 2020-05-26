<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateAccountType;
use App\Services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * Method to create a new account on the website
     * @Route("/inscription", name="user_add")
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader)
    {
        $user = new User;

        $userForm = $this->createForm(CreateAccountType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $userPassword = $userForm->getData()->getPassword();

                $encodedPassword = $passwordEncoder->encodePassword($user, $userPassword);
        
                $user->setPassword($encodedPassword);
                $user->setRoles(['ROLE_USER']);

                // We use a Services to move and rename the file
                $newName = $fileUploader->saveFile($userForm['image'], 'assets/users');
                $user->setImage($newName);

                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre compte a été créé. Vous pouvez dès à présent vous y connecter pour ajouter des recettes et des commentaires.'
                );

                return $this->redirectToRoute('main_home');
            }

        return $this->render('user/add.html.twig', [
            'userForm' => $userForm->createView(),
            'title'=>'Créer un profil'
        ]);
    }
}
