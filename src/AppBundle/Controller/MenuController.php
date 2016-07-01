<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class MenuController extends Controller
{

    /**
     * @Route("/admin/menu/saveorder", name="save_menu_position", options={"expose"=true })
     * @Method({"POST"})
     */
    public function saveOrderAction(Request $request){
        $position = $request->request->get("position");
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:MenuItem");
        for($i = 0; $i < count($position); $i++)
            $repository->find($position[$i])->setPosition($i);
        $em->flush();
        exit;
    }

    /**
     * @Route(path = "/admin/get_route", name="get_route", options={"expose"=true})
     */
    public function getRouteAction(Request $request){

        $em = $this->getDoctrine()->getManager();


        $result = $em->createQuery("SELECT a.slug,a.title FROM AppBundle:Article a")->getScalarResult();
        foreach ($result as $r){
            $route = $this->generateUrl("article",['slug'=>$r['slug']]);
            $route = str_replace("/app_dev.php","",$route);
            $routes["Article : ".$r['title']] = $route ;
        }

        $result = $em->createQuery("SELECT p.slug,p.title FROM AppBundle:Page p")->getScalarResult();
        foreach ($result as $r){
            $route = $this->generateUrl("page",['slug'=>$r['slug']]);
            $route = str_replace("/app_dev.php","",$route);
            $routes["Page : ".$r['title']] = $route ;
        }

        return $this->render('admin/routes.html.twig',['routes'=>$routes]);
    }


}
