<?php
namespace RtxLabs\DataTransformationBundle\Binder;

class Binder implements IBinder {
    private $fields = array();
    private $joins = array();
    private $bind = null;
    private $to = null;
    private $xssSecure;
    private $xssExcept = array();

    /**
     * @param boolean $xssSecure  if xssSecure is true, some html chars will be removed from strings.
     * @return DoctrineBinder
     */
    public function __construct($xssSecure=true)
    {
        $this->xssSecure = $xssSecure;
    }
    /**
     * @static
     * @return Binder
     */
    public static function create($xssSecure = true)
    {
        return new self($xssSecure);
    }

    /**
     * Binds the object defined in 'from' to an object.
     * @throws LogicException
     * @return mixed stdClass if the "to()" method was not called. Otherwise the object defined in to will be returned.
     */
    public function execute()
    {
        $result = array();

        if (!is_null($this->to)) {
             $result = $this->to;
        }

        if (is_null($this->bind)) {
            $result = null;
        }
        elseif ($this->isIterable($this->bind)) {
            foreach ($this->bind as $item) {
                $subBinder = clone $this;
                $result[] = $subBinder->bind($item)->execute();
            }
        }
        else {
            foreach ($this->fields as $field=>$closure) {
                $value = $closure;
                if (is_null($closure)) {
                    $value = $this->getValue($this->bind, $field);
                    if ($this->xssSecure && !in_array($field, $this->xssExcept) && is_string($value)) {
                        $value = preg_replace('/[^A-Za-z0-9 !@#$%^&*()\/:.]/u', '', strip_tags(html_entity_decode($value)));
                    }
                }
                elseif (!is_string($closure) && is_callable($closure)) {
                    $value = $closure($this->bind);
                }

                if ($value instanceof IBinder) {
                    $value = $value->execute();
                }

                $result = $this->setValueToFieldOfObject($value, $field, $result);
            }

            foreach ($this->joins as $field => $binder) {
                $result[$field] = $binder->bind($this->getValue($this->bind, $field))->execute();
            }
        }

        return $result;
    }

    /**
     * @param $object
     * @return bool
     */
    private function isIterable($object)
    {
        //TODO: reorder public /private

        if (is_array($object) && !$this->isAssocArray($object)) {
            return true;
        }

        if (is_object($object)) {
            $reflection = new \ReflectionObject($object);
            if ($reflection->isIterateable()) {
                return true;
            }
        }

        return false;
    }

    private function isAssocArray($value) {
        if (!is_array($value)) {
            return true;
        }

        foreach ($value as $key=>$item) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Defines a field that has to be returned in the bound result if execute will be called. The $closure will be
     * called if the field has to be created an the returned value will be the value of the field. If $closure
     * is null, the value of the object given in the "bind" method will be the value.
     *
     * @param string $field
     * @param closure $closure
     * @return Binder
     */
    public function field($field, $closure=null)
    {
        if (!key_exists($field, $this->fields)) {
            $this->fields[$field] = $closure;
        }

        return $this;
    }

    /**
     * $binder->fields(array("a", "b", "c")) is a shortcut for $binder->field("a")->field("b")->field("c")
     *
     * @param array $fields
     * @return Binder
     */
    public function fields($fields) {
        foreach ($fields as $field) {
            $this->field($field);
        }

        return $this;
    }

    /**
     * The given field will be bound by calling the execute method of the given binder.
     *
     * @param string $field
     * @param \RtxLabs\DataTransformationBundle\Binder\IBinder $binder
     * @return \RtxLabs\DataTransformationBundle\Binder\Binder
     */
    public function join($field, IBinder $binder) {
        $this->joins[$field] = $binder;
        return $this;
    }

    /**
     * Defines the object that has to be bound if the execute method is called.
     *
     * @param mixed $entity the entity or array to bind
     * @return Binder
     */
    public function bind($entity) {
        $this->bind = $entity;
        return $this;
    }

    /**
     * Defines the target object where the object given by "bind" has to be bound.
     *
     * @param object $entity
     * @return Binder
     */
    public function to($entity) {
        $this->to = $entity;
        return $this;
    }

    /**
     * @param object $object
     * @param string $field
     * @return object
     */
    private function getValue($object, $field) {
        if (is_array($object)) {
            if (array_key_exists($field, $object)) {
                return $object[$field];
            }
            else {
                return null;
            }
        }

        $reflection = new \ReflectionObject($object);

        $getter = $this->findGetter($reflection, $field);
        if ($getter != null) {
            return $getter->invoke($object);
        }

        $property = $this->findProperty($reflection, $field);
        if ($property != null) {
            return $property->getValue($object);
        }

        throw new \LogicException("unknown field: $field in " . get_class($object));
    }

    private function setValueToFieldOfObject($value, $field, $object)
    {
        assert(is_array($object) || is_object($object));
        assert(is_string($field));

        if (is_array($object)) {
            $object[$field] = $value;
        }
        else {
            $reflection = new \ReflectionObject($object);

            $setter = $this->findSetter($reflection, $field);
            if (!is_null($setter)) {
                $setter->invoke($object, $value);
            }

            $property = $this->findProperty($reflection, $field);
            if ($property != null) {
                $property->setValue($object, $value);
            }
        }

        return $object;
    }

    /**
     * @param \ReflectionObject $reflection
     * @param string $field
     * @return null|\ReflectionMethod
     */
    private function findGetter($reflection, $field) {
        $methodName = "get$field";

        if ($reflection->hasMethod($methodName)) {
            return $reflection->getMethod($methodName);
        }

        return null;
    }

    /**
     * @param \ReflectionClass $reflection
     * @param $field
     * @return void
     */
    private function findSetter($reflection, $field) {
        $methodName = "set$field";
        $method = null;

        if ($reflection->hasMethod($methodName)) {
            $method = $reflection->getMethod($methodName);
        }

        if (!is_null($method) &&
            $method->isPublic() &&
            count($method->getParameters()) === 1) {
            return $method;
        }
        else {
            return null;
        }
    }

    /**
     * @param \ReflectionObject $reflection
     * @param string $field
     * @return null|\ReflectionProperty
     */
    private function findProperty($reflection, $field) {
        $property = null;

        if ($reflection->hasProperty($field)) {
            $property = $reflection->getProperty($field);
        }

        if (!is_null($property) && $property->isPublic()) {
            return $property;
        }
        else {
            return null;
        }
    }

    /**
     * @param string $field the field that should not be cleaned
     * @return DoctrineBinder
     */
    public function xssExcept($field) {
        $this->xssExcept[] = $field;
        return $this;
    }
}
