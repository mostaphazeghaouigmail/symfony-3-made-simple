<?php

namespace AppBundle\Controller;

use AppBundle\Traits\ApiCapable;
use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ApiController extends Controller
{


    /**
     * @Route("/api/{model}", name="api_get_all")
     * @Method({"GET"})
     */
    public function getEntitesAction($model){

        if(!$model)
            return $this->handleParamError('model');

        $model  = ucfirst($model);

        if(!class_exists("AppBundle\\Entity\\".$model))
            return $this->handleNotExistError();

        if(!$this->isAuthorized($model))
            return $this->handleAuthorizationError();

       return $this->sendResponse($this->getAll($model));

    }
/*
    /**
     * @Route("/api/{model}/edit/{id}", name="api_edit_entity")
     * @Method({"PUT"})
     */
    /*
    public function editEntityAction(Request $request, $model,$id){

        if(!$model)
            return $this->handleParamError('model');

        if(!$id)
            return $this->handleParamError('id');

        $model  = ucfirst($model);

        if(!class_exists("AppBundle\\Entity\\".$model))
            return $this->handleNotExistError();

        if(!$this->isAuthorized($model))
            return $this->handleAuthorizationError();

        $serializer = $this->container->get('jms_serializer');

        $entity = $serializer->deserialize($request->getContent(),"AppBundle\\Entity\\".$model,'json');

        $em = $this->getDoctrine()->getEntityManager();
        $em->merge($entity);
        $em->flush();

        return $this->sendResponse($this->serialize($entity));

    }
*/

    /*
    /**
     * @Route("/api/{model}/create", name="api_create_entity")
     * @Method({"POST"})
     */
    /*
    public function editEntityAction(Request $request, $model){

        if(!$model)
            return $this->handleParamError('model');

        $model  = ucfirst($model);

        if(!class_exists("AppBundle\\Entity\\".$model))
            return $this->handleNotExistError();

        if(!$this->isAuthorized($model))
            return $this->handleAuthorizationError();

        $serializer = $this->container->get('jms_serializer');
        $entity = $serializer->deserialize($request->getContent(),"AppBundle\\Entity\\".$model,'json');

        $em = $this->getDoctrine()->getEntityManager();
        $em->presist($entity);
        $em->flush();

        return $this->sendResponse($this->serialize($entity));

    }
*/

    /**
     * @Route("/api/{model}/{id}", name="api_get")
     * @Method({"GET"})
     */
    public function getEntityAction($model = false,$id = false){

        if(!$model)
            return  $this->handleParamError('model');

        if(!$id)
            return $this->handleParamError('id');

        $model  = ucfirst($model);

        if(!class_exists("AppBundle\\Entity\\".$model))
            return $this->handleNotExistError();

        if(!$this->isAuthorized($model))
            return $this->handleAuthorizationError();

        return $this->sendResponse($this->getById($model,$id));

    }

    private function isAuthorized($model){
        return property_exists("AppBundle\\Entity\\".$model,'isApiCapable');
    }

    private function handleAuthorizationError(){
        $response  = new JsonResponse(['success'=>false,'data'=> 'this entity is off Api']);
        $response->setStatusCode(403);
        return $response;
    }

    private function handleNotExistError(){
        $response  = new JsonResponse(['success'=>false,'data'=> 'this entity does not exist']);
        $response->setStatusCode(500);
        return $response;
    }

    private function handleParamError($param){
        $response  = new JsonResponse(['success'=>false,'data'=> $param.' is required']);
        $response->setStatusCode(400);
        return $response;
    }

    private function getAll($model){

        $em     = $this->container->get('doctrine.orm.entity_manager');
        $repo   = $em->getRepository("AppBundle:".$model);
        $data   = $repo->findAll();

        return $data;

    }

    private function getById($model,$id){

        $em     = $this->container->get('doctrine.orm.entity_manager');
        $repo   = $em->getRepository("AppBundle:".$model);
        $data   = $repo->find($id);

        return $data;
    }

    private function serialize($data,$success = true){

        $serializer = $this->container->get('jms_serializer');
        $data = $serializer->serialize(['success'=>$success,'data'=>$data], 'json');

        return $data;
    }

    private function sendResponse($data){

        if(is_null($data) || !$data)
            $data = [];

        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent($this->serialize($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }





}
