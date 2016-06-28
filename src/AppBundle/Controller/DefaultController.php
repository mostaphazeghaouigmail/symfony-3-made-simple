<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $service = $this->get('app.application.service');
        $indexPage = $service->getParameter('index_page');
        if($indexPage && !empty($indexPage)){
            return $this->forward('AppBundle:Page:index',['slug'=>$indexPage]);
        }
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/set_current", name="set_current", options={"expose"=true})
     */
    public function setCurrentAction(Request $request)
    {
        $session = $this->get('session');
        $session->set("parentClass",$request->request->get('parentClass'));
        $session->set("parentId",$request->request->get('parentId'));

        return new JsonResponse(['success'=>true]);
    }

}
