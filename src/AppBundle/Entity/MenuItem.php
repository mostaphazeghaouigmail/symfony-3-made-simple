<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 01/07/2016
 * Time: 11:58
 */

namespace AppBundle\Entity;

use AppBundle\Traits\ApiCapable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\Table(name="MenuItem")
 */
class MenuItem
{
    use ApiCapable;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $route;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="childrens")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent", )
     */
    protected $childrens;

    /**
     * @ORM\Column(type="integer")
     * @var string
     */
    protected $position = 0;

    /**
     * @ORM\Column(type="array",nullable=true)
     * @var array
     */
    protected $attributes;

    /**
     * @ORM\Column(name="css_class",type="string",nullable=true)
     * @var array
     */
    protected $cssClass;


    public function __construct()
    {
        $this->childrens = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return MenuItem
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $route = str_replace("/app_dev.php","",$route);
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return MenuItem
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return MenuItem
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return MenuItem
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return MenuItem
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return MenuItem
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param array $cssClass
     * @return MenuItem
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @param mixed $children
     * @return MenuItem
     */
    public function setChildrens($childrens)
    {
        $this->childrens = $childrens;
        return $this;
    }

    /**
     * @param mixed $children
     * @return MenuItem
     */
    public function addChildrens($children)
    {
        $this->childrens[] = $children;
        return $this;
    }







}