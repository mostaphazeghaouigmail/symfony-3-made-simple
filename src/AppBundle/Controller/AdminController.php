<?php
namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Finder\Finder;
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

    /**
     * Override EasyAdminBundle because of a form error on delete
     * @return RedirectResponse
     */
    protected function deleteAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_DELETE);

        if ('DELETE' !== $this->request->getMethod()) {
            return $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $id = $this->request->query->get('id');
        $form = $this->createDeleteForm($this->entity['name'], $id);
        $form->handleRequest($this->request);

        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $this->dispatch(EasyAdminEvents::PRE_REMOVE, array('entity' => $entity));
        $this->executeDynamicMethod('preRemove<EntityName>Entity', array($entity));

        $this->em->remove($entity);
        $this->em->flush();

        $this->dispatch(EasyAdminEvents::POST_REMOVE, array('entity' => $entity));
        $refererUrl = $this->request->query->get('referer', '');

        $this->dispatch(EasyAdminEvents::POST_DELETE);

        return !empty($refererUrl)
            ? $this->redirect(urldecode($refererUrl))
            : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
    }

    /**
     * Override EasyAdminBundle because of a form error on delete
     */
    private function executeDynamicMethod($methodNamePattern, array $arguments = array())
    {
        $methodName = str_replace('<EntityName>', $this->entity['name'], $methodNamePattern);

        if (!is_callable(array($this, $methodName))) {
            $methodName = str_replace('<EntityName>', '', $methodNamePattern);
        }

        return call_user_func_array(array($this, $methodName), $arguments);
    }

    /**
     * @Route(path = "/admin/get_templates/{model}", name="get_templates", options={"expose"=true})
     */
    public function getTemplatesAction(Request $request,$model){

        $finder = new Finder();
        $finder->in($this->get('kernel')->getRootDir()."/Resources/views/".$model.'/templates/')->files();
        $templates = [];
        foreach ($finder as $file) {
            $templates[] = $file->getFileName();
        }
        return $this->render('admin/templates.html.twig',['model'=>$model,'templates'=>$templates]);
    }
    



}