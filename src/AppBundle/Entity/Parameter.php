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
 * @ORM\Table(name="Parameter")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Parameter
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
    protected $cle;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $valeur;

    private $mandatory = [
        'index_page',
        'tracking_code',
        'validated_comments_by_defaut',
        'allow_anonymous_comments',
        'site_email',
        'site_description',
        'site_nom'
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
     * @return Parameter
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCle()
    {
        return $this->cle;
    }

    /**
     * @param string $cle
     * @return Parameter
     */
    public function setCle($cle)
    {
        $this->cle = $cle;
        return $this;
    }

    /**
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * @param string $valeur
     * @return Parameter
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
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
     * @return Parameter
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
        if(in_array($this->getCle(),$this->mandatory) || $this->getId() < 7){
            throw new Exception('You can not remove this setting');
        }
    }

    







}
