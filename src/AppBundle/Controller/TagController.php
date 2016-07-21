<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/07/2016
 * Time: 15:42
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TagController extends SuperController
{
    /**
     * @Route("/tag/{tag}", name="tags")
     */
    public function searchByTag($tag)
    {

        $em = $this->getDoctrine()->getManager();
        $searchable = $this->get("app.application.service")->listTaggable();
        $results = [];

        foreach ($searchable as $repo) {
            $query = $em->createQuery("SELECT s FROM " . $repo . " s WHERE s.tags LIKE :key ");
            $query->setParameter('key', '%' . $tag . '%');
            $results = (!$results) ? $query->getResult() : array_merge($results, $query->getResult());
        }

        return $this->template("search.html.twig", ['results' => $results, 'tag' => $tag]);
    }
}