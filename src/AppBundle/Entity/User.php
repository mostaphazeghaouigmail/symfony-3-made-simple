<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Traits\ApiCapable;
use AppBundle\Traits\Imageable;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    use Imageable;
    use ApiCapable;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    public function __construct()
    {
        $this->images = [];
        parent::__construct();
        // your own logic
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image)
            $this->updatedAt = new \DateTime('now');

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /*
     * To retreive image on view
     * do app_twig.getImage(entity)
     * */
    public function getImage()
    {
        return $this->image;
    }






}
