<?php

namespace AppBundle\Traits;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 02/07/2016
 * Time: 18:35
 */


trait Taggable
{
    public  $isTaggable = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true )
     * @var string
     */
    protected $tags;

    public function __toString()
    {
        $model = get_class($this);
        $model = explode('\\', $model);
        $model = array_pop($model);

        return strtolower($model);
    }

    /**
     * @return ArrayCollection
     */
    public function getExplodedTags()
    {
        if($this->tags)
            return explode(' ',$this->tags);

        return [];
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }


    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }


}