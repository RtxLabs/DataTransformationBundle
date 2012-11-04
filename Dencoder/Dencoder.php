<?php
namespace RtxLabs\DataTransformationBundle\Dencoder;
 
class Dencoder {

    /**
     * @param $object
     * @return string
     */
    public static function encode($object) {
        return json_encode(self::flatten($object));
    }

    /**
     * @param string $data
     * @return stdClass
     */
    public static function decode($data) {
        assert(is_string($data));
        return json_decode($data);
    }

    private static function flatten($object) {
        assert(is_array($object) || $object instanceof \IteratorAggregate);

        $result = array();

        foreach ($object as $key => $value) {
            if ($value instanceof \DateTime) {
                $result[$key] = $value->getTimestamp();
            }
            elseif (is_array($value)) {
                $result[$key] = self::flatten($value);
            }
            elseif (is_object($value) && method_exists($value, "getId")) {
                $result[$key] = $value->getId();
            }
            elseif (is_object($value) && property_exists($value, "id")) {
                $result[$key] = $value->id;
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
