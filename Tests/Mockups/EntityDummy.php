<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups;

class EntityDummy {
    private $id = 400;
    private $firstName = "dummyFirstName";
    private $lastName = "dummyLastName";
    private $parent = null;
    private $childs = array();

    /**
     * @var string
     */
    public $hobby = "Quidditch";

    /**
     * @var \DateTime|null
     */
    public $createdAt = null;

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    /**
     * @param int $value
     * @return void
     */
    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setLastName($value)
    {
        $this->lastName = $value;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param EntityDummy $value
     * @return void
     */
    public function setParent($value)
    {
        $this->parent = $value;
    }

    /**
     * @return null|\RtxLabs\DataTransformationBundle\Tests\Mockups\EntityDummy
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function addChild($child) {
        $this->childs[] = $child;
    }

    /**
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChilds() {
        return $this->childs;
    }
}
