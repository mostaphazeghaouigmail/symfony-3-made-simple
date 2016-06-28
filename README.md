project
=======

##Do##

- composer install
- php bin/console app:init
- php bin/console assets:install --symlink

Done !

##Bundle##

- Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
- Symfony\Bundle\SecurityBundle\SecurityBundle(),
- Symfony\Bundle\TwigBundle\TwigBundle(),
- Symfony\Bundle\MonologBundle\MonologBundle(),
- Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
- Symfony\Bundle\AsseticBundle\AsseticBundle(),
- Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
- Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
- FOS\UserBundle\FOSUserBundle(),
- FOS\JsRoutingBundle\FOSJsRoutingBundle(),
- JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),
- Vich\UploaderBundle\VichUploaderBundle(),
- Liip\ImagineBundle\LiipImagineBundle(),
- Ivory\CKEditorBundle\IvoryCKEditorBundle(),


##Plus##

###Images###
You can attach a lot of image to any entity by using Imgeable Trait, add a property named 'model' and set for the class name without the namespace, for exemple 'User'
```
use AppBundle\Traits\Imageable;
class User{
    Use Imageable

}
```

###Comments###
Any entity can be commentable by using Commentable Trait, add a property named 'model' and set for the class name without the namespace, for exemple 'User'
```
use AppBundle\Traits\Commentable;
class User{
    Use Commentable

}
```