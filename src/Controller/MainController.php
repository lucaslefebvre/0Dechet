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
use Symfony\Component\Security\Core\User\UserInterface;

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

        $secretKey = '6LfROqMZAAAAAJrcinhNGi9nDeaO1EKf-pIPY2Fw';
        $responseKey = $request->request->get('g-recaptcha-response');
        $userIP = $_SERVER['REMOTE_ADDR'];

        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$responseKey.'&remoteip='.$userIP.'';
        $response = file_get_contents($url);

        $response = json_decode($response);

      
        if ($form->isSubmitted() && $form->isValid() && $response->success == true ) {
           
        
            $email = $form->get('email')->getData(); 
            $subject = $form->get('subject')->getData();
            $message = $form->get('message')->getData(); 
            
            $emailToSend = (new TemplatedEmail())

            ->from($email)
            ->to('equipe0dechet@gmail.com')
            ->subject('Formulaire de contact')
            ->htmlTemplate('email/contact/send.html.twig')
            ->context([
                'mail' => $email,
                'subject' => $subject,
                'message' => $message,
            ]);
    
            $mailer->send($emailToSend);

            $mailConfirm = (new TemplatedEmail())

            ->from('equipe0dechet@gmail.com')
            ->to($email)
            ->subject('0\'Déchet - Confirmation de votre demande de contact')
            ->htmlTemplate('email/contact/confirmation.html.twig')
            ->context([
                'subject' => $subject,
                'message' => $message,
            ]);

            $mailer->send($mailConfirm);

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé.'
            );

            return $this->redirectToRoute('main_home');
        }

        return $this->render('main/contact.html.twig', [
            'title'=>'Contact',
            'form' => $form->createView(),
        ]);

    }
  

    /**
     * Method to display the legal mentions
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function MentionLegal()
    {
        return $this->render('main/mentions_legales.html.twig', [
            'title'=>'Mentions légales',
        ]);
    }

    /**
     * Method for the team page
     * @Route("/notre-equipe", name="team")
     */
    public function team()
    {
        return $this->render('main/team.html.twig', [
            'title'=>'Notre équipe',
        ]);
    }

}
