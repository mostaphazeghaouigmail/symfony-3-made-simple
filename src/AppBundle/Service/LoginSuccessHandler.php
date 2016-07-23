<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request,TokenInterface $token){
        $session = $request->getSession();

        if($session->has('_security.main.target_path'))
            $route = $session->get('_security.main.target_path');
        else
            $route = $this->router->generate('easyadmin') ;

        return new JsonResponse(['success'=>true,'url'=> $route ]);
    }

}