<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Traits\ApiCapable;
use AppBundle\Traits\Commentable;
use AppBundle\Traits\Imageable;
use AppBundle\Traits\Taggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="Page")
 * @Vich\Uploadable
 */
class Page extends Editorial
{
    use Commentable;
    use Taggable;
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
    protected $image;

    /**
     * @Vich\UploadableField(mapping="page_images", fileNameProperty="image")
     * @var File
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $metaDescription;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $body;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $template;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $style;





}
