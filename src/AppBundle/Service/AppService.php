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
class AppService
{

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getImage($entity){

        if(!method_exists($entity,'getImage'))
            return '';

        $model = get_class($entity);
        $model = explode('\\', $model);
        $model = array_pop($model);
        $path = $this->container->getParameter('app.path.'.$model.'_images');
        return $path."/".$entity->getImage();
    }

    public function getCommentForm($entity){

        if(method_exists($entity,"getCommentable")){
            //allow anonymous ?
            $allowanonymous = $this->getParameter("allow_anonymous_comments",BooleanType::class);

            //get the form
            $form = $this->container->get('app.comment.service')
                ->getCommentForm($entity,$allowanonymous);

            //return  view form if needed
            return $form ? $this->container->get('twig')
                ->render($this->templating("comment/form.html.twig"),['form'=>$form->createView()]) : '';
        }
    }

    public function getCommentList($entity){
        if(method_exists($entity,"getCommentable")){
            return $this->container->get('twig')
                ->render($this->templating("comment/list.html.twig"),['entity'=>$entity]);
        }
    }

    public function getMap($id = 'map', $lat = 45.7573657, $lng = 4.8406775, $content="I'm Here"){
        return $this->container->get('twig')
            ->render($this->templating("component/map/map.html.twig"),[
            'id'        => $id,
            'lat'       => $lat,
            'lng'       => $lng,
            'content'   => $content
        ]);
    }

    public function getSlider($entity){
        return $this->container->get('twig')
            ->render($this->templating("component/slider/slider.html.twig"),[
            'entity'       => $entity
        ]);
    }

    public function getMenu(){
        $em     = $this->container->get('doctrine.orm.entity_manager');
        $items  = $em->getRepository("AppBundle:MenuItem")->findBy([],['position'=>"ASC"]);
        $currentSlug = $this->container->get('request_stack')->getCurrentRequest()->attributes->get('slug');
        return $this->container->get('twig')
            ->render($this->templating("component/menu/menu.html.twig"),['items'=>$items,'current'=>$currentSlug]);
    }

    public function getContactForm(){
        $form = $this->container->get('form.factory')
            ->create(ContactType::class);

        return $this->container
            ->get('twig')
            ->render($this->templating("component/contact/form.html.twig"),
                ['contact_form'=> $form->createView()]
            );
    }

    public function getAnalitycsTracking($code = ''){

        if(empty($code))
            $code = $this->getParameter('tracking_code');

        if($code){
            return $this->container
                         ->get('twig')
                         ->render($this->templating("component/analitycs/tracking.html.twig"),
                             ['code'=> $code]
                         );
        }
        else
            return '';
    }

    public function getParameter($cle,$type = false){
        $em     = $this->container->get('doctrine.orm.entity_manager');
        $param  = $em->getRepository('AppBundle:Parameter')->findOneByCle($cle);
        $param  = $param ? $param->getValeur() : '';

        if($type){
            switch ($type){
                case BooleanType::class:
                    switch (true){
                        case empty($param):
                        case is_null($param):
                        case strtolower($param) == "0":
                        case strtolower($param) == "no":
                        case strtolower($param) == "non":
                        case strtolower($param) == "nop":
                        case strtolower($param) == "x":
                        $param = false;
                            break;
                        default :
                            $param = true;
                    }
                    break;
            }
        }

        return $param;
    }

    public function templating($view){
        return $this->getTheme().'/'.$view;
    }

    public function getTheme(){
        $session = $this->container->get('session');
        if(!$session->has('theme')){
            $em     = $this->container->get('doctrine.orm.entity_manager');
            $theme  = $em->getRepository('AppBundle:Theme')->findOneBy(['active'=>true]);
            $session->set('theme',$theme && $theme->getFolderCreated() == "Yes" ? $theme->getFolder() : 'default');
        }
        return 'themes/'.$session->get('theme').'/';
    }

    public function getThemeBase(){
        return $this->getTheme().'base.html.twig';
    }



}