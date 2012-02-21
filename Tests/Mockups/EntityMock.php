<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups;
 
class EntityMock {
    private static $id_count = 1;

    public $id;

    public function __construct()
    {
        $this->id = self::$id_count++;
    }
}
