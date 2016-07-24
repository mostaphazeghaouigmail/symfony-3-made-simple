<?php

namespace AppBundle\Entity;

use AppBundle\Traits\ApiCapable;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Comment")
 */
class Comment
{
    use ApiCapable;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     * @var string
     */
    protected $text;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var string
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $parentClass;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $parentId;

    /**
     * @ORM\Column(type="boolean")
     * @var integer
     */
    private $validated = true;

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
     *  @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $email;

    protected $editable = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Comment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return Comment
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass ? ucfirst($this->parentClass) : null;
    }

    /**
     * @param string $parentClass
     * @return Comment
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;
        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     * @return Comment
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @return int
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * @param int $validated
     * @return Comment
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return Comment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param boolean $editable
     * @return Comment
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return Comment
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Comment
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getOwner(){
        if($this->getParentClass() && $this->getParentId()){
            echo "<a href='?entity=".$this->getParentClass()."&action=show&menuIndex=1&submenuIndex=-1&id=".$this->getParentId()."'>".$this->getParentClass()." ".$this->getParentId()."</a>";
            return '';
        }
    }

    public function getUser(){
        if($this->getUserId()){
            echo "<a href='?entity=User&action=show&menuIndex=1&submenuIndex=-1&id=".$this->getUserId()."'>User ".$this->getUserId()."</a>";
            return '';
        } else {
            return $this->author;
        }
    }












}
