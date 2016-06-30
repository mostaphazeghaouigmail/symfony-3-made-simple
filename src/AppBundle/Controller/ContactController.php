<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @Route("/contact/send", name="contact")
     * @Method({"POST"})
     */
    public function sendAction(Request $request)
    {

        $contact = $request->request->get('contact');
        $service = $this->get('app.application.service');
        $from    = $service->getParameter('site_email');

        if(!$from || empty($from)){
            return new JsonResponse(['success'=>false,'message'=> "There is no mail configured in settings."]);
        }
        $message = \Swift_Message::newInstance()
            ->setSubject($contact['subject'])
            ->setFrom($from)
            ->setReplyTo($contact['email'])
            ->setTo($from)
            ->setBody(
                $this->renderView(
                    'emails/contact.html.twig',
                    array('contact' => $contact)
                ),
                'text/html'
            )
        ;
        $response = $this->get('mailer')->send($message);

        return new JsonResponse(['success'=>$response,'message'=> ($response ? 'Thank you !' : "Ouups")]);
    }

}
