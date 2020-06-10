<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\DeleteType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/commentaire", name="comment_")
*/
class CommentController extends AbstractController
{
    /**
     * Method to edit an existing comment. Send a form, receive the response and flush to the Database
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/edition/{id}", name="edit", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function edit(Comment $comment, Request $request)
    {
        $this->denyAccessUnlessGranted('EDIT', $comment);

        // Setup formOptions to submit on this route even in the case of an Ajax Request
        $formOptions = ['method' => 'POST', 'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId()])];
        $form = $this->createForm(CommentType::class, $comment, $formOptions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a été modifié'
            );

            return $this->redirectToRoute('recipe_show', [
                'slug' => $comment->getRecipe()->getSlug(),
            ]);
        }
        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()])
        ]);
        
        if ($request->isXmlHttpRequest()) {  // If the request is an Ajax Call
            return $this->render('comment/ajax.edit.html.twig', [
                'comment' => $comment,
                'form' => $form->createView(),
                'deleteForm' => $formDelete->createView(),
            ]);
        } else {  // If the request is a classic Call
            return $this->render('comment/edit.html.twig', [
                'comment' => $comment,
                'form' => $form->createView(),
                'deleteForm' => $formDelete->createView(),
                'title' => "Modifier un commentaire",
            ]); 
        } 
    }

        /**
     * Method to allow a user to delete one of his comment off the website
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/comment/suppression/{id}", name="delete", methods={"DELETE"}, requirements={"id": "\d+"})
     */
    public function delete(EntityManagerInterface $em, Request $request, Comment $comment)
    {
        $this->denyAccessUnlessGranted('DELETE', $comment);

        $formDelete = $this->createForm(DeleteType::class);
        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            
            $slugForRedirect = $comment->getRecipe()->getSlug();
            $em->remove($comment);
            $em->flush();

            $this->addFlash(
                'success',
                'Le commentaire a bien été supprimé.'
            );

            return $this->redirectToRoute('recipe_show', [
                'slug' => $slugForRedirect,
            ]);
        }

        return $this->redirectToRoute('comment_edit', [
            'id' => $comment->getId(),
        ]);
    }
}
