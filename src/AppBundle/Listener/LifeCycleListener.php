<?php

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 22/06/2016
 * Time: 22:50
 */
namespace AppBundle\Listener;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Page;
use AppBundle\Entity\Parameter;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;

class LifeCycleListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
       $entity = $args->getEntity();

       if(method_exists($entity,'getImageable')){
           $em = $args->getEntityManager();
           $images = $em->getRepository("AppBundle:Image")->findBy(
               [
               "parentClass"     => $entity->getModel(),
               "parentId"        => $entity->getId()
                ],
               [
                   'place'=>"ASC"
               ]);
           $entity->setImages($images);
       }

        if(method_exists($entity,'getCommentable')){
            $em = $args->getEntityManager();
            $images = $em->getRepository("AppBundle:Comment")->findBy([
                "parentClass"     => $entity->getModel(),
                "parentId"        => $entity->getId(),
                "validated"        => true
            ]);
            $entity->setComments($images);
        }

        if($entity instanceof Comment){
            if($this->container->get('security.token_storage')->getToken() && $this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) {
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                if($user && $entity->getUserId() == $user->getId()) {
                    $entity->setEditable(true);
                }
            }
        }

    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Page || $entity instanceof Article){
            if($entity->getTemplate()){
                $this->handleTempateFile($entity);
            }
        }

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Page || $entity instanceof Article){
            $this->handleTempateFile($entity);
        }

    }

  
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        //set image as orphan
        if(method_exists($entity,'getImageable')){
            $em = $args->getEntityManager();
            $em->getConnection()->exec("Update Image SET parent_class = NULL, parent_id = NULL WHERE parent_class = '".$entity->getModel()."' AND parent_id = ".$entity->getId());
        }

        //remove attached comment
        if(method_exists($entity,'getCommentable')){
            $em = $args->getEntityManager();
            $em->getConnection()->exec("DELETE FROM Comment WHERE parent_class = '".$entity->getModel()."' AND parent_id = ".$entity->getId());
        }

        //prevent to delete main settings
        if($entity instanceof Parameter){
            if(in_array($entity->getCle(),[
                'index_page',
                'tracking_code',
                'validated_comments_by_defaut',
                'allow_anonymous_comments',
                'site_email',
                'site_description',
                'site_nom'
            ])){
                throw new Exception('You can not remove this setting');
            }
        }
    }

    private function handleTempateFile($entity){
        if($entity->getTemplate()){
            $type = $entity instanceof Page ? 'page' : 'article';
            $file = "../app/Resources/views/".$type."/".$entity->getTemplate().'.html.twig';
            if(!is_file($file)){
                $fs = new Filesystem();
                $fs->copy("../app/Resources/views/default/view_template.html.twig",$file);
            }
        }
    }

}