<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Repository\RecipeRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * Method for the homepage to show the 3 lastest and the 3 gradest recipes
     * @Route("/", name="home")
     */
    public function home(RecipeRepository $recipeRepository)
    {
        return $this->render('main/home.html.twig', [
            'bestRecipes'=>$recipeRepository->findBestRecipes(),
            'latestRecipes' => $recipeRepository->findLatestRecipes(),
            'title'=>'Accueil',
        ]);
    }

    /**
     * Method for the contact page
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData(); 
            $subject = $form->get('subject')->getData();
            $message = $form->get('message')->getData(); 
            
            $emailToSend = (new TemplatedEmail())

            ->from($email)
            ->to('equipe0dechet@gmail.com')
            ->subject($subject)
            ->htmlTemplate('email/contact/send.html.twig')
            ->context([
                'subject' => $subject,
                'message' => $message,
            ]);
    
            $mailer->send($emailToSend);

            $mailConfirm = (new TemplatedEmail())

            ->from('equipe0dechet@gmail.com')
            ->to($email)
            ->subject('Confirmation de votre demande de contact')
            ->htmlTemplate('email/contact/confirmation.html.twig')
            ->context([
                'subject' => $subject,
                'message' => $message,
            ]);

            $mailer->send($mailConfirm);

            $this->addFlash(
                'success',
                'Votre email a bien été envoyé un mail de confirmation vous a été envoyé'
            );

            return $this->redirectToRoute('main_home');
        }
        
        return $this->render('main/contact.html.twig', [
            'title'=>'Contact',
            'form' => $form->createView(),
        ]);
    }
}
