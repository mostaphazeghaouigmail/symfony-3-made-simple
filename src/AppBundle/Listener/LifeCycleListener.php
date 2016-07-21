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
use AppBundle\Entity\Theme;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class LifeCycleListener
{
    private $container;
    private $itemsMenu = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
       $entity = $args->getEntity();

       if(method_exists($entity,'getImageable')){
           if(!$entity->getimages()){
               $images = $this->container->get("app.image.service")->loadImage($this->getModel($entity),$entity->getId());
               $entity->setImages($images);
           }
       }

        if(method_exists($entity,'getCommentable')){
            if(!$entity->getComments()){
                $comments = $this->container->get("app.comment.service")->loadComments($this->getModel($entity),$entity->getId());
                $entity->setComments($comments);
            }
        }

        if($entity instanceof Comment)
            $entity->setEditable($this->container->get("app.comment.service")->canModify($entity));

    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Page || $entity instanceof Article){
            $this->container->get('app.theme.service')->handleTemplateFile($entity);
        }


        if($entity instanceof Theme)
            $this->container->get('app.theme.service')->createThemeStructure($entity->getFolder());

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Page || $entity instanceof Article){
            $this->container->get('app.theme.service')->handleTemplateFile($entity);
            if($args->hasChangedField('title')){
                $this->itemsMenu = $this->container->get("app.menu.service")->beforeUpdate($args);
            }
        }

        if($entity instanceof Theme){
            if($args->hasChangedField('active')){
                if($entity->getActive() && $entity->getFolderCreated() == "No"){
                    $entity->setActive(false);
                } else {
                    $this->container->get('app.theme.service')->deactivateAllTheme();
                }
            }
        }
    }

  
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        //set image as orphan
        if(method_exists($entity,'getImageable'))
            $this->container->get('app.image.service')->cleanImages($entity);

        //remove attached comment
        if(method_exists($entity,'getCommentable'))
            $this->container->get('app.comment.service')->cleanComment($entity);

        //handle menu change if need it
        if($entity instanceof Page || $entity instanceof Article)
            $this->container->get('app.menu.service')->cleaMenu($entity);

        //deactivate theme
        if($entity instanceof Theme && $entity->isActive()){
            $this->container->get('app.theme.service')->deactivateAllTheme();
        }
    }



    public function postFlush(PostFlushEventArgs $args)
    {
        if (! empty($this->itemsMenu)) {
            $this->container->get('app.menu.service')->afterFlush($this->itemsMenu);
            $this->itemsMenu = [];
        }

        if(APC_ENABLE)
            apc_clear_cache();
    }

    private function getModel($entity){
        $model = get_class($entity);
        $model = explode('\\', $model);
        $model = array_pop($model);
        return $model;
    }

}