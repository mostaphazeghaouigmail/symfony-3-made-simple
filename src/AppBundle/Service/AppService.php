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

    /**
     * @param $entity
     * @return string
     */
    public function getImage($entity){

        if(!method_exists($entity,'getImage'))
            return '';

        $model = get_class($entity);
        $model = explode('\\', $model);
        $model = array_pop($model);
        $path = $this->container->getParameter('app.path.'.$model.'_images');
        return $path."/".$entity->getImage();
    }

    /**
     * @param $entity
     * @return string
     */
    public function getCommentForm($entity){

        if(method_exists($entity,"getCommentable")){
            //allow anonymous ?
            $allowanonymous = $this->getSetting("allow_anonymous_comments",BooleanType::class);

            //get the form
            $form = $this->container->get('app.comment.service')
                ->getCommentForm($entity,$allowanonymous);

            //return  view form if needed
            return $form ? $this->container->get('twig')
                ->render($this->templating("comment/form.html.twig"),['form'=>$form->createView()]) : '';
        }
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function getCommentList($entity){
        if(method_exists($entity,"getCommentable")){
            return $this->container->get('twig')
                ->render($this->templating("comment/list.html.twig"),['entity'=>$entity]);
        }
    }

    /**
     * @param string $id
     * @param float $lat
     * @param float $lng
     * @param string $content
     * @return mixed
     */
    public function getMap($id = 'map', $lat = 45.7573657, $lng = 4.8406775, $content="I'm Here"){
        return $this->container->get('twig')
            ->render($this->templating("component/map/map.html.twig"),[
            'id'        => $id,
            'lat'       => $lat,
            'lng'       => $lng,
            'content'   => $content
        ]);
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function getSlider($entity){
        return $this->container->get('twig')
            ->render($this->templating("component/slider/slider.html.twig"),[
            'entity'       => $entity
        ]);
    }

    /**
     * @return mixed
     */
    public function getMenu(){

        $items       = $this->container->get('app.menu.service')->getMenuItems();
        $currentSlug = $this->container->get('request_stack')->getCurrentRequest()->attributes->get('slug');

        return $this->container->get('twig')
            ->render($this->templating("component/menu/menu.html.twig"),['items'=>$items,'current'=>$currentSlug]);
    }

    /**
     * @return mixed
     */
    public function getContactForm(){
        $form = $this->container->get('form.factory')
            ->create(ContactType::class);

        return $this->container
            ->get('twig')
            ->render($this->templating("component/contact/form.html.twig"),
                ['contact_form'=> $form->createView()]
            );
    }

    /**
     * @param string $code
     * @return string
     */
    public function getAnalitycsTracking($code = ''){

        if(empty($code))
            $code = $this->getSetting('tracking_code');

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

    /**
     * @param $key
     * @param bool $type
     * @return bool|string
     */
    public function getSetting($key, $type = false){
        
        $em     = $this->container->get('doctrine.orm.entity_manager');

        $dql    = "SELECT p FROM AppBundle:Setting p WHERE p.key=:key";
        $query  = $em->createQuery($dql);
        $query->setParameter('key',$key);

        if(APC_ENABLE)
            $query->useResultCache(true,86400,'_setting_'.$key);

        $setting  = $query->getResult();
        $setting  = isset($setting[0]) ? $setting[0]->getValue() : '';


        if($type){
            switch ($type){
                case BooleanType::class:
                    switch (true){
                        case empty($setting):
                        case is_null($setting):
                        case strtolower($setting) == "0":
                        case strtolower($setting) == "no":
                        case strtolower($setting) == "non":
                        case strtolower($setting) == "nop":
                        case strtolower($setting) == "x":
                        $setting = false;
                            break;
                        default :
                            $setting = true;
                    }
                    break;
            }
        }

        return $setting;
    }

    /**
     * @param $view
     * @return string
     */
    public function templating($view){
        return $this->getTheme().'/'.$view;
    }

    /**
     * @return string
     */
    public function getTheme(){
        $session = $this->container->get('session');
        if(!$session->has('theme')){
            $em     = $this->container->get('doctrine.orm.entity_manager');
            $theme  = $em->getRepository('AppBundle:Theme')->findOneBy(['active'=>true]);
            $session->set('theme',$theme && $theme->getFolderCreated() == "Yes" ? $theme->getFolder() : 'default');
        }
        return 'themes/'.$session->get('theme').'/';
    }

    /**
     * @return string
     */
    public function getThemeBase(){
        return $this->getTheme().'base.html.twig';
    }

    /**
     * @param Request $request
     * @param $url
     * @return mixed
     */
    public function getMenuUrl(Request $request, $url){

        return $this->container->get('app.menu.service')->getMenuUrl($request,$url,$this->container->get('kernel')->getEnvironment());

    }

    /**
     * @param $model
     * @return mixed
     */
    public function getSearch($model){
        return $this->container
            ->get('twig')
            ->render($this->templating("component/search/search.html.twig"),
                ['model'=> $model]
            );
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function getTagsLink($entity){
        $tagService = $this->container->get('app.tag.service');
        return $tagService->getTagsLink($entity->getTags());
    }


    /**
     * @return array
     */
    public function listEntities(){

        $em = $this->container->get("doctrine.orm.entity_manager");
        $meta = $em->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }

        return $entities;

    }

    /**
     * @param $prop
     * @return array
     */
    private function listBehaviored($prop){
        $class = [];
        $entites  = $this->listEntities();

        foreach ($entites as $entity){
            if(property_exists($entity,$prop))
                $class[] = $entity;
        }

        return $class;
    }

    /**
     * @return array
     */
    public function listTaggable(){
        return $this->listBehaviored("tags");
    }

    /**
     * @return array
     */
    public function listImageable(){
        return $this->listBehaviored("images");
    }

    /**
     * @return array
     */
    public function listCommentable(){
        return $this->listBehaviored("comments");
    }


    /**
     * @param $model
     * @return bool|int|string
     */
    public function getBundleNameFromEntity($model)
    {
        $model   = ucfirst($model);
        $bundles = $this->container->getParameter('kernel.bundles');
        $class   = $this->getClassWithNamespace($model);
        $dataBaseNamespace = substr($class, 0, strpos($class, '\\Entity\\'));

        foreach ($bundles as $type => $bundle) {
            $bundleRefClass = new \ReflectionClass($bundle);
            if ($bundleRefClass->getNamespaceName() === $dataBaseNamespace) {
                return $type;
            }
        }

        return false;
    }

    /**
     * @param $model
     * @return bool|string
     */
    public function getClassWithNamespace($model){

        $entites = $this->listEntities();

        foreach($entites as $class){
            $class = explode("\\",$class);
            if(end($class) == $model){
                return implode("\\",$class);
            }
        }

        return false;
    }

}