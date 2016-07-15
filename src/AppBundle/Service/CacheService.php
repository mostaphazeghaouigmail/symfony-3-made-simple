<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use AppBundle\Type\ContactType;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
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
class CacheService
{

    private $env;

    public function __construct($container)
    {
        $this->container = $container;
        $this->env       = $this->container->get( 'kernel' )->getEnvironment();
    }

   public function setCache($model){
       if(APC_ENABLE){

           $dbName          = $this->container->getParameter('database_name');
           $cacheManager    = $this->container->get('cache');
           $em              = $this->container->get('doctrine.orm.entity_manager');
           $data            = $em->getRepository('AppBundle:'.$model)->findAll();

           $serializer = $this->container->get('jms_serializer');
           $data = $serializer->serialize($data, "json");

           $cacheManager->save($dbName."_".$model,$data);
       }
   }

    public function get($model){

       if(APC_ENABLE){
           $dbName          = $this->container->getParameter('database_name');
           $cacheManager    = $this->container->get('cache');
           $data            = $cacheManager->get($dbName."_".$model);

           if(!$data)
               $this->setCache($model);

           $data        = $cacheManager->get($dbName."_".$model);
           $serializer  = $this->container->get('jms_serializer');
           $data = $serializer->deserialize($data, $model);


           return $data;
       }

   }

    public function clear($model){

        $dbName          = $this->container->getParameter('database_name');
        $cacheManager    = $this->container->get('cache');
        $cacheManager->remove($dbName."_".$model);

    }





}