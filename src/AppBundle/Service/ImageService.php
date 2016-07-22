<?php
namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Kernel;


/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class ImageService
{
    private $em;
    private $rootDir;
    private $appService;

    public function __construct(EntityManager $em,Kernel $kernel,AppService $appService)
    {
        $this->em           = $em;
        $this->rootDir      = $kernel->getRootDir();
        $this->appService   = $appService;
    }

    public function cleanImages($entity){
        $model = get_class($entity);
        $model = explode('\\', $model);
        $model = array_pop($model);
        $sql = "Update Image SET parent_class = NULL, parent_id = NULL WHERE parent_class = '".$model."' AND parent_id = ".$entity->getId();
        $this->em->getConnection()->exec($sql);
    }

    public function loadImage($model,$id){

        $dql = "SELECT i FROM AppBundle:Image i WHERE i.parentClass ='".$model."' AND i.parentId=".$id." ORDER BY i.place ASC";
        $query = $this->em->createQuery($dql);
        $site = $this->appService->getParameter("site_nom");

        if(APC_ENABLE)
            $query->useResultCache(true,86400,$site."_".$model."_images_".$id);

        return $query->getResult();
    }
    
    public function changeFile($request,$path){

        $dataURL = json_decode($request->getContent());
        $parts = explode(',', $dataURL->imageFile);
        $data = $parts[1];
        $data = base64_decode($data);
        $success = file_put_contents($this->rootDir . '/../web'.$path, $data);

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