<?php
namespace RtxLabs\DataTransformationBundle\Binder;

interface IBinder {
    /**
     * @abstract
     * @param $object
     * @return IBinder
     */
    public function bind($object);

    /**
     * @abstract
     * @return mixed
     */
    public function execute();
}
