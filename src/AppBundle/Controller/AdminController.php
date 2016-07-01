<?php
namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseAdminController
{
    /**
     * @Route("/admin/")
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    public function listMenuItemAction(){
        $this->dispatch(EasyAdminEvents::PRE_LIST);
        $fields = $this->entity['list']['fields'];
        $paginator = $this->findAll($this->entity['class'], $this->request->query->get('page', 1), $this->config['list']['max_results'], 'position', 'ASC');
        $this->dispatch(EasyAdminEvents::POST_LIST, array('paginator' => $paginator));
        return $this->render($this->entity['templates']['list'], array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
        ));
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