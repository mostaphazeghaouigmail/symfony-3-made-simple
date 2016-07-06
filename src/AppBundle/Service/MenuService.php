<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;


class MenuService
{
    private $em;
    private $router;

    public function __construct(EntityManager $em,Router $router)
    {
        $this->em     = $em;
        $this->router = $router;
    }

    public function cleaMenu($entity){

        $query = $this->em->createQuery("SELECT m FROM AppBundle:MenuItem m WHERE m.route LIKE :key ");
        $query->setParameter('key', '%'.$entity->getSlug().'%');
        $itemsMenu =  $query->getResult();

        $ids = [];
        foreach ($itemsMenu as $item)
            $ids[] = $item->getId();

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



}