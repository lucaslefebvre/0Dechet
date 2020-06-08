<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CreateAccountType;
use App\Form\DeleteType;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class UserController extends AbstractController
{
    /**
     * Method to display the account page of the connected user
     * @Route("/profil/{username}", name="user_read", methods={"GET"})
     */
    public function read(User $user)
    {
        return $this->render('user/read.html.twig', [
            'user' => $user,
            'title' => 'Mon profil'
        ]);
    }

    /**
     * Method to create a new account on the website
     * @Route("/inscription", name="user_add", methods={"GET","POST"})
     */
    public function add(Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader)
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

                // OLD VERSION : We use a Services to move and rename the file
                // $newName = $fileUploader->saveFile($userForm['image'], 'assets/images/users');

                // NEW VERSION :  We retrieve the original image
                $file = $userForm['image'];

                if ($file->getData() !== null) {
                    // We retrieve the cropped image
                    $base64 = $request->request->get('photocoupee');

                    // We decode the cropped image in base 64
                    list(, $data) = explode(',', $base64);
                    $data = base64_decode($data);

                    // We rename the file with the service we created
                    $fileName = $fileUploader->createFileName($file->getData()->getClientOriginalExtension());

                    // We replace the content of the image with the info in base 64 from the cropped image
                    file_put_contents('assets/images/users/' . $fileName, $data);

                    // We set the cropped image in the user data
                    $user->setImage($fileName);
                }

                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                // We create a request for send a email of confirmation
                $email = (new TemplatedEmail())
                        ->from('equipe0dechet@gmail.com')
                        ->to($user->getEmail())
                        ->subject('Bienvenue sur 0dechet')
                        ->htmlTemplate('email/user/add.html.twig')
                        ->context([
                            'username' => $user->getUsername(),
                        ]);
                
                $mailer->send($email);

                $this->addFlash(
                    'success',
                    'Votre compte a été créé, un email de confirmation a été envoyé sur votre boîte mail. Vous pouvez dès à présent vous y connecter pour ajouter des recettes et des commentaires.'
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profil/edition/{username}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(User $user, Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('EDIT', $user);
        $imageUser = $user->getImage();

        $userForm = $this->createForm(CreateAccountType::class, $user);

        $userForm->handleRequest($request);

            if ($userForm->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
              
                if ($userForm->isValid()){
                    $userPassword = $userForm->get('password')->getData();

                    // We modify the password only if the user modified it
                    if ($userPassword !== null) {
                        $user->setPassword($passwordEncoder->encodePassword($user, $userPassword));
                    }

                    // We keep the old image if there is no new image uploaded
                    if ($user->getImage() === null) {
                        $user->setImage($imageUser);
                    }
                  
                    // OLD VERSION : We use a Services to move and rename the file
                    // $newName = $fileUploader->saveFile($userForm['image'], 'assets/images/users');
                    // $user->setImage($newName);

                    // NEW VERSION :  We retrieve the original image
                    $file = $userForm['image'];

                    if ($file->getData() !== null) {
                        // We retrieve the cropped image
                        $base64 = $request->request->get('photocoupee');

                        // We decode the cropped image in base 64
                        list(, $data) = explode(',', $base64);
                        $data = base64_decode($data);

                        // We rename the file with the service we created
                        $fileName = $fileUploader->createFileName($file->getData()->getClientOriginalExtension());

                        // We replace the content of the image with the info in base 64 from the cropped image
                        file_put_contents('assets/images/users/' . $fileName, $data);

                        // We set the cropped image in the user data
                        $user->setImage($fileName);
                    }

                    $em->flush();

                    // We create a request for send a email of confirmation
                    $email = (new TemplatedEmail())
                    ->from('equipe0dechet@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Modification de votre profil 0dechet')
                    ->htmlTemplate('email/user/edit.html.twig')
                    ->context([
                                'username' => $user->getUsername(),
                            ]);
                    
                    $mailer->send($email);

                    $this->addFlash(
                        'success',
                        'Votre compte a bien été modifié, un email de confirmation a été envoyé.'
                    );

                    return $this->redirectToRoute('user_read', [
                        'username' => $user->getUsername(),
                    ]);
                } else {
                    $em->refresh($user);
                }
            }
            
        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('user_delete', ['username' => $user->getUsername()])
        ]);

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm->createView(),
            'deleteForm' => $formDelete->createView(),
            'title'=>'Modifier son profil',
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Method to allow a user to delete his/her account on the website
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profil/suppression/{username}", name="user_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $em, MailerInterface $mailer, Request $request, User $user)
    {
        
        $this->denyAccessUnlessGranted('DELETE', $user);

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {

            //You have to clear the Session before deleting user entry in DB
            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $user->getId())
            {
              $session = $this->get('session');
              $session = new Session();
              $session->invalidate();
            }
            //Then remove the user
            $em->remove($user);
            $em->flush();

            // We create a request for send a email of confirmation

            $email = (new TemplatedEmail())
            ->from('equipe0dechet@gmail.com')
            ->to($user->getEmail())
            ->subject('Confirmation de suppression de votre compte 0dechet')
            ->htmlTemplate('email/user/delete.html.twig')
            ->context([
                        'username' => $user->getUsername(),
                    ]);
    
            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre compte a bien été supprimé.'
            );

            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('user_edit', [
            'username' => $user->getUsername(),
        ]);
    }

}
