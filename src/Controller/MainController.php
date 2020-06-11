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
      
        if ($form->isSubmitted() && $form->isValid() && $this->captchaverify($request->get('g-recaptcha-response'))) {
            
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
        if($form->isSubmitted() &&  $form->isValid() && !$this->captchaverify($request->get('g-recaptcha-response'))){
                 
            $this->addFlash(
                'error',
                'Captcha obligatoire'
              );             
        }

        
        return $this->render('main/contact.html.twig', [
            'title'=>'Contact',
            'form' => $form->createView(),
        ]);

    }

    function captchaverify($recaptcha){
        $url = "https://www.google.com/recaptcha/api/siteverify";
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            "secret"=>"6LeN2KIZAAAAAAGCZqJdlfm4_ZqO-fiu_X6kWoIP","response"=>$recaptcha));
            $response = curl_exec($ch);
            curl_close($ch);
        }else{
            $response = file_get_contents($url);
        }
        $data = json_decode($response);     
    
    return $data->success;        
    }

    /**
     * Method for the legal mentions
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function MentionLegal(RecipeRepository $recipeRepository)
    {
        return $this->render('main/mentions_legales.html.twig', [
            'title'=>'Mentions légales',
        ]);
    }

    /**
     * Method for the team page
     * @Route("/notre-equipe", name="team")
     */
    public function team(RecipeRepository $recipeRepository)
    {
        return $this->render('main/team.html.twig', [
            'title'=>'Notre équipe',
        ]);
    }

}
