<?php
namespace RtxLabs\DataTransformationBundle\Tests\Binder;

use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use RtxLabs\DataTransformationBundle\Tests\Mockups\EntityMock;

class DencoderTest extends \PHPUnit_Framework_TestCase {
    public function testEncode()
    {
        $example = array("firstName"=>"Uwe", "lastName"=>"Klawitter");
        $result = Dencoder::encode($example);

        $expected = array("firstName"=>"Uwe", "lastName"=>"Klawitter");

        $this->assertEquals(json_encode($expected), $result);
    }

    public function testEncodeWithDateTime()
    {
        $dateTime = new \DateTime();

        $result = Dencoder::encode(array("createdAt"=>$dateTime));

        $expected = array("createdAt"=>$dateTime->getTimestamp());

        $this->assertEquals(json_encode($expected), $result);
    }

    public function testEncodeWithObject()
    {
        $example = array("parent"=>new EntityMock());
        $result = Dencoder::encode($example);

        $expected = array("parent"=>$example["parent"]->id);

        $this->assertEquals(json_encode($expected), $result);
    }

    public function testEncodeWithArray()
    {
        $example = array(new EntityMock(), new EntityMock(), new EntityMock());
        $result = Dencoder::encode($example);

        $expected = array($example[0]->id, $example[1]->id, $example[2]->id);

        $this->assertEquals(json_encode($expected), $result);
    }

    public function testEncodeWithChildArray() {
        $example = array("childs"=>array(new EntityMock(), new EntityMock(), new EntityMock()));
        $result = Dencoder::encode($example);

        $expected = array("childs"=>array());
        $expected["childs"][] = $example["childs"][0]->id;
        $expected["childs"][] = $example["childs"][1]->id;
        $expected["childs"][] = $example["childs"][2]->id;

        $this->assertEquals(json_encode($expected), $result);
    }
}
