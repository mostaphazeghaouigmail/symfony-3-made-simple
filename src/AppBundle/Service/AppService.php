<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use AppBundle\Type\ContactType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\Mapping\Cache;
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

    private $env;

    public function __construct($container)
    {
        $this->container = $container;
        $this->env       = $this->container->get( 'kernel' )->getEnvironment();
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

        $items       = $this->container->get('app.menu.service')->getMenuItems();
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

        $dql    = "SELECT p FROM AppBundle:Parameter p WHERE p.cle=:cle";
        $query  = $em->createQuery($dql);
        $query->setParameter('cle',$cle);

        if(APC_ENABLE)
            $query->useResultCache(true,86400,'_parameter_'.$cle);

        $param  = $query->getResult();
        $param  = isset($param[0]) ? $param[0]->getValeur() : '';


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

    public function getMenuUrl(Request $request,$url){

        return $this->container->get('app.menu.service')->getMenuUrl($request,$url,$this->container->get('kernel')->getEnvironment());

    }

    public function getSearch($model){
        return $this->container
            ->get('twig')
            ->render($this->templating("component/search/search.html.twig"),
                ['model'=> $model]
            );
    }

    public function getTagsLink($entity){
        $tagService = $this->container->get('app.tag.service');
        return $tagService->getTagsLink($entity->getTags());
    }


    public function listEntities(){

        $em = $this->container->get("doctrine.orm.entity_manager");
        $meta = $em->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }

        return $entities;

    }

    private function listBehaviored($prop){
        $class = [];
        $entites  = $this->listEntities();

        foreach ($entites as $entity){
            if(property_exists($entity,$prop))
                $class[] = $entity;
        }

        return $class;
    }

    public function listTaggable(){
        return $this->listBehaviored("tags");
    }

    public function listImageable(){
        return $this->listBehaviored("images");
    }

    public function listCommentable(){
        return $this->listBehaviored("comments");
    }

}