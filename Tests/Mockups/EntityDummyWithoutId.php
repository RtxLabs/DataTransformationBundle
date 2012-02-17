<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups;
 
class EntityDummyWithoutId {
    private $firstName = "dummyFirstName";
    private $lastName = "dummyLastName";
    private $parent = null;

    public $hobby = "Quidditch";

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
}
