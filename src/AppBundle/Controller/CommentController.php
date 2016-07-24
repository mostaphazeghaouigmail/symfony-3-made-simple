<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $connected      = $this->container->get('security.authorization_checker')->isGranted('ROLE_USER');
        $form = $this->createForm(CommentType::class,$comment,['connected'=>$connected]);
        $form->handleRequest($request);

        $bundle = $this->get("app.application.service")->getBundleNameFromEntity($comment->getParentClass());
        $object = $em->getRepository($bundle.":".ucfirst($comment->getParentClass()))->find($comment->getParentId());

        $valid = false;

        if($form->isValid() && $object->isCommentOpen()){
            $em->persist($comment);
            $em->flush();
            $this->sendAdminMail($comment);
            $valid = true;
        }

        if($request->isXmlHttpRequest()){
            if($comment->getValidated()){
                if($valid)
                    return $this->render($this->templating("comment/comment.html.twig"),['comment'=>$comment]);
                else
                    return new JsonResponse(['success'=>false,'message'=>((string)$form->getErrors(true))]);
            }
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

    private function sendAdminMail(Comment $comment){

        $email  = $this->get('app.application.service')->getSetting("site_email");

        if(!$email || empty($email))
            return false;

        $em         = $this->getDoctrine()->getManager();
        $mailer     = $this->get('mailer');

        $bundle = $this->get("app.application.service")->getBundleNameFromEntity($comment->getParentClass());
        $object     = $em->getRepository($bundle.":".ucfirst($comment->getParentClass()))->find($comment->getParentId());

        $objectLink = $this->generateUrl('easyadmin',
            array(
                'action' => 'show',
                'entity' => ucfirst($comment->getParentClass()),
                'id' =>$comment->getParentId()
            )
            ,UrlGeneratorInterface::ABSOLUTE_URL
        );

        $data   = array(
            'comment'       => $comment,
            'object'        => $object,
            'objectLink'    => $objectLink
        );

        if($comment->getUserId()){
            $author             = $em->getRepository("AppBundle:User")->find($comment->getUserId());
            $data['author']     = $author;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject("New comment posted on ".ucfirst($comment->getParentClass())." : ".$comment->getParentId())
            ->setFrom($email)
            ->setReplyTo($email)
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    $this->templating('emails/comment.html.twig'),
                    $data
                ),
                'text/html'
            )
        ;
        $mailer->send($message);
    }

}
