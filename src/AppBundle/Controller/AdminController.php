<?php
namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseAdminController
{
    /**
     * @Route("/admin")
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * @Route(path = "/show_front", name="show_front")
     */
    public function showFrontAction(Request $request){
        $id = $request->query->get('id');
        $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:'.$request->query->get('entity'))->find($id);
        return new RedirectResponse($this->generateUrl(strtolower($request->query->get('entity')),['slug'=>$entity->getSlug()]));
    }

    /**
     * @Route(path = "/show_parent", name="show_parent")
     */
    public function showParentAction(Request $request){
        $id = $request->query->get('id');
        $entity = $this->getDoctrine()->getManager()->getRepository('AppBundle:'.$request->query->get('entity'))->find($id);
        return $this->redirectToRoute('easyadmin', array(
            'action' => 'show',
            'id' => $entity->getParentId(),
            'entity' => $entity->getParentClass(),
        ));
    }

    
    
    
}