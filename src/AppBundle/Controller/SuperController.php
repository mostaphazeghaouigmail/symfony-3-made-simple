<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 06/07/2016
 * Time: 15:08
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SuperController extends Controller
{

    public function templating($view){
        return $this->get('app.application.service')->templating($view);
    }
}