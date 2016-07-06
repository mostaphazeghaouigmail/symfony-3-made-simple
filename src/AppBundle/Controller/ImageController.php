<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Type\ImageTypeLight;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends SuperController
{

    /**
     * @Route("/image/{slug}", name="image")
     * @ParamConverter("image", class="AppBundle\Entity\Image", options={"slug" = "slug"})
     */
    public function showAction(Request $request, Image $image)
    {
        return $this->render($this->templating('image/templates/view.html.twig'), [
            'entity' => $image
        ]);
    }

    /**
     * @Route("/admin/image/edit/{id}", name="image_edit", options={"expose"=true })
     * @Method({"GET"})
     */
    public function editAction(Request $request,Image $image)
    {
        $form = $this->createForm(ImageTypeLight::class,$image,[
            'action' => $this->generateUrl('image_post_edit',['id'=>$image->getId()])
        ]);

        return $this->render('image/imageEdit.html.twig', [
            'image'   => $image,
            'form'   => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/image/edit/post/{id}", name="image_post_edit", options={"expose"=true })
     * @Method({"POST"})
     */
    public function postEditAction(Request $request,Image $image)
    {

        $form = $this->createForm(ImageTypeLight::class,$image,[
            'action' => $this->generateUrl('image_post_edit',['id'=>$image->getId()])
        ]);

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new JsonResponse(['success'=>true]);
    }

    /**
     * @Route("/admin/image/edit/post/file/{id}", name="image_post_edit", options={"expose"=true })
     * @Method({"POST"})
     */
    public function postEditFileAction(Request $request,Image $image)
    {
        // get the dataURL
        $dataURL = json_decode($request->getContent());
        $parts = explode(',', $dataURL->imageFile);
        $data = $parts[1];
        $data = base64_decode($data);

        $appService = $this->get("app.application.service");
        $path = $appService->getImage($image);

        $name =
        // write the file to the upload directory
        $success = file_put_contents($this->get('kernel')->getRootDir() . '/../web'.$path, $data);

        $this->get('liip_imagine.cache.manager')->remove($path);
        return new JsonResponse(['success'=>$success]);
    }


    /**
     * @Route("/admin/image/delete/{id}", name="image_delete", options={"expose"=true })
     * @Method({"GET"})
     */
    public function deleteAction(Request $request,Image $image)
    {
        $path = $this->get('app.application.service')->getImage($image);
        $this->get('liip_imagine.cache.manager')->remove($path);
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();
        return new JsonResponse(['success'=>true]);

    }

    /**
     * @Route("/admin/image/get", name="images_get", options={"expose"=true })
     * @Method({"GET"})
     */
    public function getAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $session = $this->get('session');

        if($request->query->get('from_entity')){
            $dql   = "SELECT i FROM AppBundle:Image i WHERE i.parentId = ".$session->get('parentId')." AND i.parentClass = '".$session->get('parentClass')."'";
        } else {
            $dql   = "SELECT i FROM AppBundle:Image i";
        }

        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('image/imageList.html.twig', [
            'allImages'=> $pagination

        ]);

    }
    /**
     * @Route("/admin/image/saveorder", name="save_image_position", options={"expose"=true })
     * @Method({"POST"})
     */
    public function saveOrderAction(Request $request){
        $position = $request->request->get("position");
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("AppBundle:Image");
        for($i = 0; $i < count($position); $i++)
            $repository->find($position[$i])->setPlace($i);
        $em->flush();
        exit;
    }

    private function getRandomName(){
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < 20; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

}
