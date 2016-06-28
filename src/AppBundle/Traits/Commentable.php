<?php
namespace AppBundle\Traits;

use Symfony\Component\Config\Definition\Exception\Exception;

//Todo Rendre le load lazy ?
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 22/06/2016
 * Time: 23:19
 */
trait Commentable
{

    private $comments;

    /**
     * @return Json
     */
    public function getCommentable(){
        return json_encode(['name'=>$this->getModel(),'id'=>$this->id]);
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $images
     * @return Page
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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