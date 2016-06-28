<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class CommentService
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    public function getCommentForm($entity,$allowAnonymous = false){

        $service        = $this->container->get('app.application.service');
        $connected      = $this->container->get('security.authorization_checker')->isGranted('ROLE_USER');
        $user           = $this->container->get('security.token_storage')->getToken()->getUser();
        $allowanonymous = $service->getParameter("allow_anonymous_comments",BooleanType::class);
        $validByDefault = $service->getParameter("allow_anonymous_comments",BooleanType::class);

        if($allowAnonymous || $connected){
            $comment = new Comment();
            $comment->setParentClass($entity->getModel())
                    ->setParentId($entity->getId())
                    ->setValidated($allowAnonymous);

            if($connected){
                $comment->setUserId($user->getId())
                        ->setAuthor($user->getUsername());
            }

            $form = $this->container->get('form.factory')
                ->create(
                    CommentType::class,
                    $comment,
                    ['action'=>$this->container->get('router')->generate('post_comment')]
                );

            return $form;
        }

        return false;

    }


}