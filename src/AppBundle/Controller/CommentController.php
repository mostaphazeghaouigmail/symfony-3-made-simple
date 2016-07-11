<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends SuperController
{
    /**
     * @Route("/comment/post", name="post_comment")
     * @Method({"POST"})
     */
    public function postCommentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isValid()){
            $em->persist($comment);
            $em->flush();
        }

        if($request->isXmlHttpRequest()){
            if($comment->getValidated())
                return $this->render($this->templating("comment/comment.html.twig"),['comment'=>$comment]);
        }
        else
            return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/comment/update/{id}", name="update_comment", options={"expose":true})
     * @Method({"POST"})
     */
    public function updateCommentAction(Request $request, Comment $comment)
    {
        $em     = $this->getDoctrine()->getManager();
        $user   = $this->getUser();

        if(empty($request->request->get('text')) || $user->getId() != $comment->getParentId())
            exit;

        $comment->setText($request->request->get('text'));
        $em->flush();

        exit;

    }

}
