<?php
namespace RtxLabs\DataTransformationBundle\Binder;

class GetMethodBinder implements IBinder {

    /**
     * @var \RtxLabs\DataTransformationBundle\Binder\Binder
     */
    private $bind;
    private $except = array();
    private $fields = array();
    private $joins = array();
    private $to;

    private $whitelisting;

    /**
     * @static
     * @return \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    public static function create($whitelisting=true)
    {
        return new self($whitelisting);
    }

    public function __construct($whitelisting=true)
    {
        $this->whitelisting = $whitelisting;
    }

    /**
     * @return \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    public function bind($entity)
    {
        $this->bind = $entity;
        return $this;
    }

    public function except($field) {
        $this->except[] = $field;
        return $this;
    }

    /**
     * @param $field
     * @param $closure
     * @return GetMethodBinder
     */
    public function field($field, $closure=null) {
        $this->fields[$field] = $closure;
        return $this;
    }

    public function join($field, $binder) {
        $this->joins[$field] = $binder;
        return $this;
    }

    /**
     * @param object $entity
     * @return GetMethodBinder
     */
    public function to($entity) {
        $this->to = $entity;
        return $this;
    }

    /**
     * @return object
     */
    public function execute()
    {
        if ($this->bind === null) {
            return null;
        }

        $result = array();

        if (is_array($this->bind) && !$this->isAssocArray($this->bind)) {
            foreach ($this->bind as $item) {
                $result[] = $this->bind($item)->execute();
            }
        }
        else {
            $binder = Binder::create()->bind($this->bind)->to($this->to);

            foreach ($this->fields as $field=>$closure) {
                $binder->field($field, $closure);
            }

            foreach ($this->joins as $field => $joinedBinder) {
                $binder->join($field, $joinedBinder);
            }

            foreach ($this->bind as $key=>$value) {
                if ($this->isWhitelisted($key) && !in_array($key, $this->except)) {
                    $binder->field($key, $value);
                }
            }

            if (is_object($this->bind)) {
                $reflection = new \ReflectionObject($this->bind);

                foreach ($reflection->getMethods() as $method) {
                    //TODO: remove methodReturnsSymfonyCollection
                    if ($this->isGetter($method)
                        && !$this->methodReturnsSymfonyCollection($method)) {

                        $fieldName = lcfirst(substr($method->getName(), 3));

                        if ($this->isWhitelisted($fieldName) && !in_array($fieldName, $this->except)) {
                            $binder->field($fieldName);
                        }

                    }
                }

                foreach ($reflection->getProperties() as $property) {
                    if ($property->isPublic()
                            && $this->isWhitelisted($property->getName())
                            && !in_array($property->getName(), $this->except)) {

                        $binder->field($property->getName());
                    }
                }
            }

            $result = $binder->execute();
        }

        return $result;
    }

    public function isAssocArray($array)
    {
        return is_array($array) && array_values($array) !== $array;
    }

    /**
     * @param \ReflectionMethod $method
     * @return boolean
     */
    private function methodReturnsSymfonyCollection($method)
    {
        if (strpos($method->getDocComment(), '@return Doctrine\Common\Collections\Collection') !== false) {
            return false;
        }
        else {
            return false;
        }
    }

    private function isGetter(\ReflectionMethod $method)
    {
        $result = true;

        if (substr($method->getName(), 0, 3) != "get") {
            $result = false;
        }

        if (!$method->isPublic()) {
            $result = false;
        }

        if (!$method->getNumberOfRequiredParameters() == 0) {
            $result = false;
        }

        return $result;
    }

    private function isWhitelisted($field)
    {
        if ($this->whitelisting) {
            return array_key_exists($field, $this->fields);
        }

        return true;
    }
}
