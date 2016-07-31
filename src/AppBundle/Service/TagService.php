<?php
namespace AppBundle\Service;

use AppBundle\Entity\Comment;
use AppBundle\Type\CommentType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Router;


/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 20/06/2016
 * Time: 16:01
 */
class TagService
{
    private $env;
    private $router;

    public function __construct(Kernel $kernel,Router $router)
    {
        $this->env      = $kernel->getEnvironment();
        $this->router   = $router;
    }

    /**
     * @return string
     */
    public function getTagsLink($tags){

        $tags = explode(" ",$tags);
        $link = [];

        $env = $this->env == "prod" ? "" : "/app_dev.php";
        foreach ($tags as $tag){
            $url = $this->router->generate("tags",['tag'=>$tag]);
            $link[] = "<a href='".$url."'?tag=1>".$tag."</a>";
        }

        return implode("",$link);
    }



}