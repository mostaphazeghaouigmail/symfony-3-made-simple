<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Page;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class PageController extends Controller
{
    
    /**
     * @Route("/{slug}", name="page")
     * @ParamConverter("page", class="AppBundle\Entity\Page", options={"slug" = "slug"})
     */
    public function showAction(Request $request, Page $page)
    {
        return $this->render('page/'.($page->getTemplate() ? $page->getTemplate()  : 'view').'.html.twig', [
            'entity' => $page
        ]);
    }



}
