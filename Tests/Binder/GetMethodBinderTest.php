<?php
namespace RtxLabs\DataTransformationBundle\Tests\Binder;

use RtxLabs\DataTransformationBundle\Binder\GetMethodBinder;
use RtxLabs\DataTransformationBundle\Tests\Mockups\EntityDummy;
use RtxLabs\DataTransformationBundle\Tests\Mockups\EntityMock;
use RtxLabs\DataTransformationBundle\Tests\Mockups\EntityDummyWithoutId;
use RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMock;

class GetMethodBinderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf("RtxLabs\DataTransformationBundle\Binder\GetMethodBinder", GetMethodBinder::create());
    }

    public function testBindWithoutEntity()
    {
        $this->assertNull(GetMethodBinder::create()->execute());
    }

    //test bind list

    public function testBindArray() {
        $array = array();
        $array[0] = new EntityDummy();
        $array[0]->setFirstName("Uwe");
        $array[1] = new EntityDummy();
        $array[1]->setFirstName("Time");
        $array[2] = new EntityDummyWithoutId();
        $array[2]->setFirstName("Harald");

        $result = GetMethodBinder::create()->bind($array)->execute();

        $this->assertEquals(3, count($result));
        $this->assertEquals(4, count($result[2]));
        $this->assertEquals($array[0]->getFirstName(), $result[0]["firstName"]);
        $this->assertEquals($array[1]->getFirstName(), $result[1]["firstName"]);
        $this->assertEquals($array[2]->getFirstName(), $result[2]["firstName"]);
    }

    public function testBindTo() {
        $bind = array("firstName"=>"Uwe", "lastName"=>"Klawitter");
        $to = new EntityDummy();

        GetMethodBinder::create()->bind($bind)->to($to)->execute();

        $this->assertEquals($bind["firstName"], $to->getFirstName());
        $this->assertEquals($bind["lastName"], $to->getLastName());
    }

    public function testBindField() {
        $bind = new EntityMock();

        $closure = function($entity) {
            return $entity->id * 2;
        };

        $result = GetMethodBinder::create()
                ->bind($bind)
                ->field("calc", $closure)
                ->execute();

        $this->assertEquals($closure($bind), $result["calc"]);
    }

    public function testExcept() {
        $now = new \DateTime();

        $bind = new UserMock();
        $bind->setUsername("uklawitter");
        $bind->setDeletedAt($now->format("r"));

        $result = GetMethodBinder::create()
            ->bind($bind)
            ->except("deletedAt")
            ->execute();

        $this->assertEquals($bind->getUsername(), $result["username"]);
        $this->assertArrayNotHasKey("deletedAt", $result);
    }

    public function testBindEmptyArray() {
        $result = GetMethodBinder::create()->bind(array())->execute();
        $this->assertEquals(array(), $result);
    }

    private function assertBound($value, $property, $entity)
    {
        $result = EntityBinder::create()->from($entity)->field($property)->bind();
        $this->assertEquals($value, $result[$property]);
    }
}
