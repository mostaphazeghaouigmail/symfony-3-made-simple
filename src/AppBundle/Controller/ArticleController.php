<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ArticleController extends Controller
{
    /**
     * @Route("/blog/{slug}", name="article")
     * @ParamConverter("article", class="AppBundle\Entity\Article", options={"slug" = "slug"})
     */
    public function indexAction(Request $request, Article $article)
    {

        return $this->render('article/'.($article->getTemplate() ? $article->getTemplate()  : 'view').'.html.twig', [
            'entity' => $article
        ]);
    }



}
