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
     * @Route("/inscription", name="user_add", methods={"GET","POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main_home');
        }

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

    /**
     * Method to edit an existing account on the website
     * @Route("/profil/edition/{id}", name="user_edit", methods={"GET","POST"}, requirements={"id": "\d+"})
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader)
    {
        $userForm = $this->createForm(CreateAccountType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $userPassword = $userForm->getData()->getPassword();

                // We modify the password only if the user modified it
                if ($userPassword !== null) {
                    $user->setPassword($passwordEncoder->encodePassword($user, $userPassword));
                }

                // We use a Services to move and rename the file
                $newName = $fileUploader->saveFile($userForm['image'], 'assets/users');
                $user->setImage($newName);

                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre compte a bien été modifié.'
                );

                return $this->redirectToRoute('main_home');
            }

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm->createView(),
            'title'=>'Modifier son profil'
        ]);

    }
}
