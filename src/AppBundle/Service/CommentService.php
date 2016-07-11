<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class CommentService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function cleanComment($entity){
        $sql = "Update Image SET parent_class = NULL, parent_id = NULL WHERE parent_class = '".$entity->getModel()."' AND parent_id = ".$entity->getId();
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->exec($sql);
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


    public function loadComments($model,$id){
        return $this->container->get('doctrine.orm.entity_manager')
            ->getRepository("AppBundle:Comment")
            ->findBy(["parentClass"=> $model, "parentId"=> $id,'validated' => true], ['validated'=>"ASC"]);
    }

    public function canModify($entity){
        $token           = $this->container->get('security.token_storage')->getToken();
        $securityChecker = $this->container->get('security.authorization_checker');
        if($token && $securityChecker->isGranted('ROLE_USER')) {
            $user    = $this->container->get('security.token_storage')->getToken()->getUser();
            $isAdmin = $securityChecker->isGranted('ROLE_ADMIN');
            if($entity->getUserId() == $user->getId() || $isAdmin ){
                return true;
            }
        }

        return false;
    }

}