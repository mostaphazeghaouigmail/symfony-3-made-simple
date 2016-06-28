<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Type\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UploaderController extends Controller
{
    /**
     * @Route("/uploader/{parentId}/{parentClass}", name="uploader", options={"expose"=true })
     */
    public function uploaderAction(Request $request,$parentId,$parentClass)
    {
        $em     = $this->getDoctrine()->getManager();
        $images = $em->getRepository("AppBundle:Image")->findBy(
            [
                "parentClass"     => $parentClass,
                "parentId"        => $parentId
            ],
            ['place'=>"ASC"]);
         // replace this example code with whatever you need
        return $this->render('uploader/uploader.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
            'images'   => $images,
            "parentClass"     => $parentClass,
            "parentId"        => $parentId
        ]);
    }

    /**
     * @Route("/upload/post", name="post_upload", options={"expose"=true })
     */
    public function postUpload(Request $request){
        $em = $this->getDoctrine()->getManager();

        $image = new Image();
        $image->setImageFile($request->files->get('file'));
        $image->setParentClass($request->request->get('parentClass'));
        $image->setParentId($request->request->get('parentId'));

        $em->persist($image);
        $em->flush();

        return $this->render('image/imageItem.html.twig', [
            'image'   => $image,
        ]);
    }
}
