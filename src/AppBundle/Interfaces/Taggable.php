<?php

namespace AppBundle\Interfaces;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/07/2016
 * Time: 16:03
 */
interface Taggable
{
    public function getSlug();
    public function __toString();

}