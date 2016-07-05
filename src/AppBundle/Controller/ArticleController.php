<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog")
 */
class ArticleController extends Controller
{

    /**
     * @Route("/", name="articles")
     */
    public function indexAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $dql   = "SELECT a FROM AppBundle:Article a";

        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('article/index.html.twig', [
            'entities' => $pagination
        ]);
    }


    /**
     * @Route("/{slug}", name="article")
     */
    public function showAction(Request $request, $slug)
    {
        die($slug);
        return $this->render('article/templates/'.($article->getTemplate() ? $article->getTemplate()  : 'view').'.html.twig', [
            'entity' => $article
        ]);
    }




}
