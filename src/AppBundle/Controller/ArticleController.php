<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Interfaces\Taggable;
use Doctrine\Common\Cache\ApcCache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog")
 */
class ArticleController extends SuperController
{

    /**
     * @Route("/", name="articles")
     */
    public function indexAction(Request $request)
    {


        $dql   = "SELECT a FROM AppBundle:Article a";
        $pagination = $this->getArticles($request,$dql);

        return $this->template('article/index.html.twig', [
            'entities' => $pagination
        ]);
    }


    /**
     * @Route("/search", name="quick_search", options={"expose"=true})
     */
    public function quickSearchAction(Request $request ){

        $term       = $request->request->get('term');
        $properties = get_class_vars("AppBundle\\Entity\\Article");

        $conditions = [];

        foreach ($properties["searchable"] as $f)
            $conditions[] = "a.".$f." LIKE '%".$term."%'";

        $conditions = implode(" OR ",$conditions);
        $pagination = $this->getArticles($request,'SELECT a from AppBundle:Article a WHERE '.$conditions);

        return $this->template('article/index.html.twig', [
            'entities' => $pagination,
            'term'     => $term
        ]);

    }


    /**
     * @Route("/{slug}", name="article")
     * @ParamConverter("article", class="AppBundle\Entity\Article", options={"slug" = "slug"})
     */
    public function showAction(Request $request, Article $article)
    {
        $template = $article->getTemplate() ? $article->getTemplate() : "view";

        $view = $this->templating('article/templates/'.($article->getTemplate() ? $article->getTemplate()  : 'view').'.html.twig');
        if(!file_exists($this->get('kernel')->getRootDir().'/Resources/views/'.$view) && $template !="view" ){
            $template = "view";
        }
        return $this->template('article/templates/'.$template.'.html.twig', [
            'entity' => $article
        ]);
    }

    private function getArticles(Request $request,$dql){

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);

        if(APC_ENABLE)
            $query->useResultCache(true,3600,'_articles');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        return $pagination;
    }

}
