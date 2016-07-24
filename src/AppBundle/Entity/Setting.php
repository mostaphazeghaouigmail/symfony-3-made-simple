<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Traits\Commentable;
use AppBundle\Traits\Imageable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="Setting")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Setting
{

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
    protected $key;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $value;

    private $mandatory = [
        'index_page',
        'tracking_code',
        'validated_comments_by_defaut',
        'allow_anonymous_comments',
        'site_email',
        'site_description',
        'site_name'
    ];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Setting
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Setting
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return Setting
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @ORM\PreRemove
     */
    public function beforeRemove(){
        if(in_array($this->getKey(),$this->mandatory) || $this->getId() <= 7){
            throw new Exception('You can not remove this setting');
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeUpdate(){
        if(!in_array($this->getKey(),$this->mandatory) && $this->getId() <= 7){
            throw new \Exception('You can not change this key');
        }
    }

    







}
