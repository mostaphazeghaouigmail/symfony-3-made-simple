Symfony 3 Made Simple starter kit
=======

###Documentation under construction###

<p>Hi! This is my Symfony 3 starter kit. It's certainly not perfect, so feel free to use and improve upon it. It comes with a bunch of nice bundles and a couple of basic blocks: image management with drag and drop and multi upload, simple comment management, simple widgets (ahem... very simple, don't get your hopes too high). It's really not a cms, you're not supposed to take it like it is. It has been created to be modified for your unique use-cases. With this starter, you can manage: Users, Pages, Articles, Images, Comments, Settings.</p>

<h3>Main Entity Management</h3>

<p>Easy User management with  <a href="http://symfony.com/doc/current/bundles/FOSUserBundle/index.html" style="line-height: 1.6;">FosUserBundle</a></p>

<p>Easy Backoffice with the excellent <a href="https://github.com/javiereguiluz/EasyAdminBundle" style="line-height: 1.6;">EasyAdminBundle</a>, config file is config/easy_admin.yml</p>

<p>Esay Uploading with <a href="https://github.com/dustin10/VichUploaderBundle">VichUploaderBundle</a>, config file is config/vich_uploader.yml, variable path are in config/parameters.yml</p>

<p>Easy Image format gestion with&nbsp;<a href="https://github.com/liip/LiipImagineBundle">ImagineBundle</a></p>

<p>Easy text editing with <a href="https://github.com/egeloen/IvoryCKEditorBundle">IvoryCKEditorBundle</a></p>

<h3>Inside</h3>

<ul style="padding-left: 0;">
    <li>Symfony\Bundle\FrameworkBundle\FrameworkBundle</li>
    <li>Symfony\Bundle\SecurityBundle\SecurityBundle</li>
    <li>Symfony\Bundle\TwigBundle\TwigBundle</li>
    <li>Symfony\Bundle\MonologBundle\MonologBundle</li>
    <li>Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle</li>
    <li>Symfony\Bundle\AsseticBundle\AsseticBundle</li>
    <li>Doctrine\Bundle\DoctrineBundle\DoctrineBundle</li>
    <li>Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle</li>
    <li>FOS\UserBundle\FOSUserBundle</li>
    <li>FOS\JsRoutingBundle\FOSJsRoutingBundle</li>
    <li>JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle</li>
    <li>Vich\UploaderBundle\VichUploaderBundle</li>
    <li>Liip\ImagineBundle\LiipImagineBundle</li>
    <li>Ivory\CKEditorBundle\IvoryCKEditorBundle</li>
</ul>

<h3>Image Management</h3>
<p>MultiUpload, Drag and drop, sortable, resize</p>
<p>Juste use Imageable Trait in your entity</p>

<pre>use AppBundle\Traits\Imageable;

class Page
{
    use Imageable;

}

</pre>

<p>
    That's it! After that you can retreive your image with <code>entity.getImages()</code><br><br>
    To show them on your view : <code>asset("uploads/image/"~image.image)|imagine_filter('thumbnail')</code><br><br>
    Filters are defined in <code>app/config/liip_imagine.yml</code>
</p>


<h3>Map</h3>
<p>
    Just a tiny widget, type <code>app_twig.getMap(your_lat,your_lng)|raw </code> on your view
</p>
<p>
    You can change it easily on <code>app/Resources/views/component/map</code>
</p>

<h3>Comment</h3>
<p>Juste use Imageable Trait in your entity</p>
<pre>
use AppBundle\Traits\Commentable;

class Page
{
    use Commentable;

}

</pre>
<p>That's it! After that you can retreive your comment with <code>entity.getComments()</code>  </p>
<p>To get the form : <code>app_twig.getCommentForm(entity)|raw</code>  </p>
<p>To get a predefined list, type <code> app_twig.getCommentList(entity)|raw </code>, template are located in <code>app/Resources/views/comment</code>
<p>You can allow and disallow anonymous comments by changing parameter allow_anonymous_comments to 0 or nop or non or no or x   </p>
<p>You can set the comments to be published by default or not by changing the following parameter: validated_comments_by_defaut to 0 or nop or non or no or x </p>

<h3>Tracking</h3>
<p>
    Another tiny widget, type <code>app_twig.getAnalitycsTracking('your tracking code')|raw </code> on your view
</p>
<p>
    If you set the parameter named tracking_code just do  <code>app_twig.getAnalitycsTracking()|raw </code>
</p>
<p>
    It will output the default analitycs code tracking
</p>

<pre>    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'your_code' ]);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</pre>

<p>
    Feel free to customize it, it's located in <code>views/component/analitycs/</code>
</p>

<h3>Slider<br></h3>
<p>
   Just a tiny widget again (i know, i know...), Just type <code>app_twig.getSlider(entity)|raw </code> on your view
</p>
<p>
    It's the bootstrap slider. For now, it's made for imgeable entities, feel free to make your own :)
</p>
<p>
   It's located in <code>views/component/slider/</code>
</p>

<h3>Contact Form</h3>
<p>Type <code>app_twig.getContactForm()</code>, you can customize fields on <code>src/Type/ContactType.php</code>, the view is located on app/Resources/views/emails/contact<p>
<p>You may need to set your configuration on parameters.yml</p>

<h3>Settings</h3>
<p>Some settings are locked, but you can remove them by editing the file <code>AppBundl/Listener/LifeCycleSliner.php</code></p>
<p>List of default settings : </p>
<ul style="padding : 0">
    <li>    Default Slug route	</li>
    <li>    Analitycs tracking  </li>
    <li>    By default, comments published without moderation</li>
    <li>	Allow anonymous comment</li>
    <li>	Contact Email	</li>
    <li>	Website description </li>
    <li>	Website name</li>
</ul>

<h3>Route</h3>
<p>/{slug}          -> page </p>
<p>/blog            -> articles index</p>
<p>/blog/{slug}     -> article page</p>
<p>/image/{slug}    -> image page</p>

<h3>Page and Article</h3>
<p>- Page and Article are by default commentable</p>
<p>- The template property create a template file located in app/Resources/views/article or page</p>
<p>- The property style add the style on the head of the page<p>


<h3>Menu</h3>
<p>Simple Menu Management (one level for now)</p>
<p>Position are changeable by drag and drop</p>
<p>if a menu item is related to a page or an article and he's parent title is modifed, the item uri is modified to</p>
<p><p>To get the main menu on view, type <code>app_twig.getMenu()|raw</code></p>


<h3>Twig Helper</h3>
<p>There is a twig helper named app_twig, the class is located at src/AppBundle/Service/AppService.php</p>
<p>It's also a service, you can use it on every controlleur</p>

<h4>Image</h4>
<p>Retrieve Image on view for image property (Vich) : <code>app_service.getImage(entity)</code></p>
<p>Get e bootstrap slider for imageable entities : <code>app_service.getSlider(entity)|raw</code></p>


<h4>Comment</h4>
<p>Get the comment Form for commentable entities : <code>app_service.getCommentForm(entity)|raw</code></p>
<p>Get the comment List for commentable entities : <code>app_service.getcommentList(entity)|raw</code></p>

<h4>MAP</h4>
<p>Get the map widget : <code>app_service.getMap(id_map,lat,lng,content)</code></p>

<h4>Analitycs</h4>
<p>Get analitycs tracking script code : <code>app_service.getAnalitycsTracking(code)|raw</code></p>

<h4>Setting</h4>
<p>Retreive a setting's value : <code>app_service.getParameter(key,type = false)</code><p>

<h4>Menu</h4>
<p>Get the main menu on view : <code>app_twig.getMenu()|raw</code><p>


<h3>API</h3>
<p>There is a tiny little API</p>
<p>If you want your entity api capable, just use ApiCapable trait in it </p>
<p>You can customize the public / private on security.yml for method, by default, PUT and POST method require an admin role</p>
<p>It's just made for building for exemple a front with angular or somthing like that, you can set the api_key in parameters.yml</p>
<p>You need to add api_key in get parameter</p>
<h4>Method and route</h4>
<p>GET /api/{model}         -> Retreive all entity of a model</p>
<p>GET /api/{model}/{id}    -> Retreive entity of a model with id</p>
<p>PUT /api/{model}         -> update entity of a model</p>
<p>POST /api/{model}        -> create entity of a model</p>

<h3>Installation</h3>
<p><code>git clone https://github.com/sohrabg/made-simple.git</code></p>
<p><code>composer install</code></p>
<p>Create an empty database</p>
<p><code>php bin/console app:init</code></p>
<p><code>php bin/console assets:install --symlink</code></p>
<p>Answer all question</p>
<p>Start to work</p>
<p>Demo online comming soon...</p>


<h3>INFORMATIONS</h3>
<p>I a m working on this currently, so you may have to make <code>php bin/console doctrine:schema:update --force</code> if you pull the project</p>


