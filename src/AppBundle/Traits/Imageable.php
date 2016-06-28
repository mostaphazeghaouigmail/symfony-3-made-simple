<?php
namespace AppBundle\Traits;

use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 22/06/2016
 * Time: 23:19
 */
trait Imageable
{

    private $images;

    /**
     * @return Json
     */
    public function getImageable(){
        return json_encode(['name'=>$this->getModel(),'id'=>$this->id]);
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     * @return Page
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        $model = get_class($this);
        $model = explode('\\', $model);
        $model = array_pop($model);
        return $model;
    }

}