<?php
namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;


/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class ImageService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function cleanImages($entity){
        $sql = "Update Image SET parent_class = NULL, parent_id = NULL WHERE parent_class = '".$entity->getModel()."' AND parent_id = ".$entity->getId();
        $this->em->getConnection()->exec($sql);
    }

    public function loadImage($model,$id){
        return $this->em->getRepository("AppBundle:Image")->findBy(["parentClass"=> $model, "parentId"=> $id], ['place'=>"ASC"]);
    }
    
    public function changeFile($request,$path){

        $dataURL = json_decode($request->getContent());
        $parts = explode(',', $dataURL->imageFile);
        $data = $parts[1];
        $data = base64_decode($data);
        $success = file_put_contents($this->get('kernel')->getRootDir() . '/../web'.$path, $data);

        return boolval($success);
    }

    public function saveOrder($request){
        $position = $request->request->get("position");
        $repository = $this->em->getRepository("AppBundle:Image");
        for($i = 0; $i < count($position); $i++)
            $repository->find($position[$i])->setPlace($i);
        $this->em->flush();
    }


}