<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 06/07/2016
 * Time: 23:21
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Kernel;

class ThemeService
{
    private $rootDir;
    private $appService;
    private $em;
    private $session;

    public function __construct(Kernel $kernel,AppService $appService,EntityManager $em,Session $session)
    {
        $this->rootDir      = $kernel->getRootDir();
        $this->appService   = $appService;
        $this->em           = $em;
        $this->session      = $session;

    }

    public function createThemeStructure($folder)
    {
        //theme folder to create
        $targetThemePath       = $this->getThemePath($folder);
        //assest folder to create for symlink
        $targetThemeAssetsPath = $this->getWebThemePath($folder).'/assets';

        $fs = new Filesystem();
        if(!$fs->exists($targetThemePath)){
            //copy theme directory to a theme directory
            $fs->mirror($this->getThemePath('default'),$targetThemePath);
            //create web theme directory for assets symlink
            $fs->mkdir($this->getWebThemePath($folder));
            //create assets symlink
            $fs->symlink($targetThemePath.'/assets/',$targetThemeAssetsPath);
        }

        return new JsonResponse(['success'=>true]);
    }

    public function linkAssets($folder){

        $targetThemePath       = $this->getThemePath($folder);
        $targetThemeAssetsPath = $this->getWebThemePath($folder).'/assets';

        $fs = new Filesystem();
        dump($fs->exists($targetThemeAssetsPath));
        if(!$fs->exists($targetThemeAssetsPath)){
            $fs->mkdir($this->getWebThemePath($folder));
            $fs->symlink($targetThemePath.'/assets/',$targetThemeAssetsPath);
        }

        return new JsonResponse(['success'=>true]);

    }

    public function handleTemplateFile($entity){
        if($entity->getTemplate()){

            $type = $entity instanceof Page ? 'page' : 'article';
            $file = "../app/Resources/views/".$this->appService->getTheme().$type."/templates/".$entity->getTemplate().'.html.twig';

            if(!is_file($file)){
                $fs = new Filesystem();
                $fs->copy("../app/Resources/views/default/view_template.html.twig",$file);
            }
        }

    }
    
    public function deactivateAllTheme(){
        $this->em->getConnection()->exec("Update Theme SET active = false");
        $this->clearThemeSession();
    }

    public function clearThemeSession(){
        $this->session->remove('theme');
    }

    private function getWebThemePath($theme = false){

        $path = $this->rootDir.'/../web/themes';

        if($theme)
            $path .= "/".$theme;

        return $path;
    }


    private function getThemePath($theme = false){
        $path = $this->rootDir."/Resources/views/themes";

        if($theme)
            $path .= "/".$theme;

        return $path;
    }



}