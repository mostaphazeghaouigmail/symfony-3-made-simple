<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 06/07/2016
 * Time: 16:34
 */

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Theme;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ThemeController extends SuperController
{
    /**
     * @Route("/admin/theme/check/{id}", name="check_theme_strucutre", options={"expose"=true })
     */
    public function checkThemeStructureAction(Request $request,Theme $theme)
    {
        return new JsonResponse(['success'=>$theme->getFolderCreated() == "Yes"]);
    }

    /**
     * @Route("/admin/theme/create_structure/{id}", name="create_theme_structure", options={"expose"=true })
     */
    public function createThemeStructureAction(Request $request,Theme $theme)
    {
        return $this->get('app.theme.service')->createThemeStructureAction($theme);
    }

}