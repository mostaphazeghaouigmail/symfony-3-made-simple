<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;


class MenuService
{
    private $em;
    private $router;
    private $appService;

    public function __construct(EntityManager $em,Router $router,AppService $appService)
    {
        $this->em           = $em;
        $this->router       = $router;
        $this->appService   = $appService;

    }

    public function cleaMenu($entity){

        $query = $this->em->createQuery("SELECT m FROM AppBundle:MenuItem m WHERE m.route LIKE :key ");
        $query->setParameter('key', '%'.$entity->getSlug().'%');
        $itemsMenu =  $query->getResult();

        $ids = [];
        foreach ($itemsMenu as $item)
            $ids[] = $item->getId();

        if( count($ids) > 0 )
            $this->em->getConnection()->exec("DELETE FROM MenuItem WHERE id IN (".implode(',',$ids).") ");
    }

    public function beforeUpdate($args){

        $collection = [];

        $entity  = $args->getObject();
        $oldSlug = $args->getOldValue('slug');
        $newSlug = $args->getNewValue('slug');

        $sql     = " SELECT m FROM AppBundle:MenuItem m WHERE m.route LIKE :key ";
        $query = $this->em->createQuery($sql);
        $query->setParameter('key', '%'.$oldSlug.'%');

        $itemsMenu =  $query->getResult();

        foreach($itemsMenu as $itemMenu){
            $itemMenu->setRoute($this->router->generate(($entity instanceof Page) ? "page" : "article",['slug'=>$newSlug]));
            $collection[] = $itemMenu;
        }

        return $collection;
    }

    public function afterFlush($collection){
        foreach ($collection as $menuItem) {
            $this->em->persist($menuItem);
        }
        $this->em->flush();
    }

    public function getMenuItems(){

        $dql    = "SELECT m FROM AppBundle:MenuItem m WHERE m.parent IS NULL ORDER BY m.position ASC";
        $query  = $this->em->createQuery($dql);
        $site   = $this->appService->getParameter("site_nom");

        if(APC_ENABLE)
            $query->useResultCache(true,86400,$site."_menu");

        return $query->getResult();
    }

    public function getMenuUrl(Request $request,$url,$env){

        if(strpos($url,"#") !== false && $request->getPathInfo() != "/"){
            $url = "/".$url;
        }
        if($env == "dev"){
            $exploded = explode("/",$url);
            if(count($exploded) > 1 ){
                $exploded[0] = "/app_dev.php";
                $url = implode("/",$exploded);
            }
        }

        return $url;
    }


}