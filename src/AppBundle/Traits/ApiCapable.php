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


trait ApiCapable
{
    public $isApiCapable = true;
    protected $key = "ofSWeZ3223yOuI8D0MDVwktks8sIDfortHQVNRf1viSpumQKeC";

    public function getKey(){
        return $this->key;
    }


}