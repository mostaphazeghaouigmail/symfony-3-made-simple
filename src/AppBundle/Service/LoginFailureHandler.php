<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class LoginFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function onAuthenticationFailure(Request $request,AuthenticationException $exception){
        return new JsonResponse(['success' => false,'message'=>$exception->getMessage()]);
    }

}