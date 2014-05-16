<?php
namespace RtxLabs\DataTransformationBundle\Tests\Binder;

use \RtxLabs\DataTransformationBundle\Binder\Binder;
use \RtxLabs\DataTransformationBundle\Tests\Mockups\EntityDummy;
use \RtxLabs\DataTransformationBundle\Tests\Mockups\EntityMock;

class BinderTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithoutFrom()
    {
        $this->assertNull(Binder::create()->execute());
    }

    public function testExecuteWithFromSucceeds()
    {
        $result = Binder::create()->bind(new EntityDummy())->execute();
        $this->assertTrue(is_array($result));
    }

    public function testExecuteWithId()
    {
        $entity = new EntityDummy();
        $entity->setId(5);
        $this->assertBound($entity->getId(), "id", $entity);
    }

    public function testExecuteWithCamelCase()
    {
        $entity = new EntityDummy();
        $entity->setFirstName("uwe");
        $this->assertBound($entity->getFirstName(), "firstName", $entity);
    }

    public function testExecuteWithoutGetter()
    {
        $entity = new EntityDummy();
        $entity->hobby = "Quidditch";
        $this->assertBound($entity->hobby, "hobby", $entity);
    }

    public function testExecuteWidthXss()
    {
        $entity = new EntityDummy();
        $entity->foo = "<script>bar</script>";

        $result = Binder::create()->bind($entity)->field("foo")->execute();
        $this->assertEquals("bar", $result['foo']);
    }

    public function testExecuteWidthoutXss()
    {
        $entity = new EntityDummy();
        $entity->bar = "<script>foo</script>";

        $result = Binder::create(false)->bind($entity)->field("bar")->execute();
        $this->assertEquals($entity->bar, $result['bar']);
    }

    public function testExecuteWidthXssExcept()
    {
        $entity = new EntityDummy();
        $entity->stillxss = "<script>xss</script>";

        $result = Binder::create()->bind($entity)->field("stillxss")
            ->xssExcept('stillxss')->execute();
        $this->assertEquals($entity->stillxss, $result['stillxss']);
    }

    public function testExecuteWidthXssExceptMultiple()
    {
        $entity = new EntityDummy();
        $entity->foo1 = "<script>bar1</script>";
        $entity->foo2 = "<script>bar2</script>";
        $entity->foo3 = "<script>bar3</script>";

        $result = Binder::create()->bind($entity)
            ->field("foo1")
            ->field('foo2')
            ->field('foo3')
            ->xssExcept('foo1')
            ->xssExcept('foo3')
            ->execute();

        $this->assertEquals($entity->foo1, $result['foo1']);
        $this->assertEquals('bar2', $result['foo2']);
        $this->assertEquals($entity->foo3, $result['foo3']);
    }

    public function testExecuteWithObject()
    {
        $parent = new EntityDummy();
        $parent->setId(55);

        $entity = new EntityDummy();
        $entity->setParent($parent);

        $this->assertBound($parent, "parent", $entity);
    }

    public function testExecuteWithFieldClosure()
    {
        $entities = array(new EntityMock(), new EntityMock(), new EntityMock());

        $closure = function($entity) {
            return $entity->id * 2;
        };

        $result = Binder::create()->bind($entities)->field("calc", $closure)->execute();

        $this->assertEquals($closure($entities[0]), $result[0]["calc"]);
        $this->assertEquals($closure($entities[1]), $result[1]["calc"]);
        $this->assertEquals($closure($entities[2]), $result[2]["calc"]);
    }

    public function testExecuteWithDate()
    {
        $entity = new EntityDummy();
        $entity->createdAt = new \DateTime();

        $this->assertBound($entity->createdAt, "createdAt", $entity);
    }

    /**
     * @expectedException LogicException
     */
    public function testExecuteWithUnknownField()
    {
        $entity = new EntityDummy();
        Binder::create()
                ->bind($entity)
                ->field("iAmUnknown")
                ->execute();
    }

    public function testBindHierarchy()
    {
        $parent = new EntityDummy();
        $parent->setFirstName("Uwe");
        $parent->setLastName("Klawitter");

        $child = new EntityDummy();
        $child->setParent($parent);

        $result = Binder::create()
                ->bind($child)
                ->join("parent", Binder::create()
                                       ->field("firstName")
                                       ->field("lastName"))
                ->execute();

        $this->assertEquals($parent->getFirstName(), $result["parent"]["firstName"]);
        $this->assertEquals($parent->getLastName(), $result["parent"]["lastName"]);
    }

    public function testBindHierarchyWithNull()
    {
        $entity = new EntityDummy();
        $entity->setParent(null);

        $result = Binder::create()
                ->bind($entity)
                ->join("parent", Binder::create()->field("firstName"))
                ->execute();

        $this->assertNull($result["parent"]);
    }

    public function testCreateReturnsBinder()
    {
        $binder = Binder::create();
        $this->assertInstanceOf('RtxLabs\DataTransformationBundle\Binder\Binder', $binder);
    }

    public function testFieldReturnsBinder()
    {
        $binder = Binder::create()->field("test");
        $this->assertInstanceOf('RtxLabs\DataTransformationBundle\Binder\Binder', $binder);
    }

    public function testBindArray() {
        $array = array();
        $array[0] = new EntityDummy();
        $array[0]->setFirstName("Uwe");
        $array[1] = new EntityDummy();
        $array[1]->setFirstName("Timo");

        $result = Binder::create()->bind($array)->field("firstName")->execute();

        $this->assertEquals($array[0]->getFirstName(), $result[0]["firstName"]);
        $this->assertEquals($array[1]->getFirstName(), $result[1]["firstName"]);
    }

    //test bind array
    //test bind list

    public function testFields()
    {
        $entity = new EntityDummy();

        $result = Binder::create()
                ->bind($entity)
                ->fields(array("id", "firstName", "lastName"))
                ->execute();
        $this->assertEquals($entity->getId(), $result["id"]);
        $this->assertEquals($entity->getFirstName(), $result["firstName"]);
        $this->assertEquals($entity->getLastName(), $result["lastName"]);
    }

    public function testFieldWithClosureReturningBinder()
    {
        $entity = new EntityMock();

        $result = Binder::create()
                ->bind($entity)
                ->field("id", function($value) {
                    return Binder::create()
                            ->bind($value)
                            ->field("idx2", function($value) {
                                return $value->id*2;
                            });
                })
                ->execute();

        $this->assertEquals($entity->id * 2, $result["id"]["idx2"]);
    }

    public function testBindReturnsBinder()
    {
        $binder = Binder::create()->bind(new EntityDummy());
        $this->assertInstanceOf('RtxLabs\DataTransformationBundle\Binder\Binder', $binder);
    }

    public function testBindTo()
    {
        $bind = array("firstName"=>"Max", "lastName"=>"Klawitter");
        $to = new EntityDummy();

        Binder::create()->bind($bind)->field("firstName")->field("lastName")->to($to)->execute();

        $this->assertEquals($bind["firstName"], $to->getFirstName());
        $this->assertEquals($bind["lastName"], $to->getLastName());
    }

    public function testBindEmptyArray() {
        $result = Binder::create()->bind(array())->execute();

        $this->assertEquals(array(), $result);
    }

    private function assertBound($value, $field, $entity)
    {
        $result = Binder::create()->bind($entity)->field($field)->execute();
        $this->assertArrayHasKey($field, $result);
        $this->assertEquals($value, $result[$field]);
    }
}
