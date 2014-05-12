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
    private $xssSecure;
    private $xssExcept = array();

    /**
     * @see DoctrineBinder::create()
     * @static
     * @return \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    public static function create($whitelisting=true, $xssSecure=true)
    {
        return new self($whitelisting, $xssSecure);
    }

    /**
     * @see GetMethodBinder::create()
     */
    public function __construct($whitelisting=true, $xssSecure=true)
    {
        $this->xssSecure = $xssSecure;
        $this->whitelisting = $whitelisting;
    }

    /**
     * @see Binder::bind()
     * @param object|array $entity
     * @return \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    public function bind($entity)
    {
        $this->bind = $entity;
        return $this;
    }

    /**
     * @see DoctrineBinder::except()
     * @param string $field
     * @return GetMethodBinder
     */
    public function except($field) {
        $this->except[] = $field;
        return $this;
    }

    /**
     * @see Binder::field()
     * @param $field
     * @param $closure
     * @return GetMethodBinder
     */
    public function field($field, $closure=null) {
        $this->fields[$field] = $closure;
        return $this;
    }

    /**
     * @see Binder::join()
     * @param string $field
     * @param IBinder $binder
     * @return GetMethodBinder
     */
    public function join($field, $binder) {
        $this->joins[$field] = $binder;
        return $this;
    }

    /**
     * @see Binder::to()
     * @param object $entity
     * @return GetMethodBinder
     */
    public function to($entity) {
        $this->to = $entity;
        return $this;
    }

    /**
     * @param string $field the field that should not be cleaned
     * @return DoctrineBinder
     */
    public function xssExcept($field) {
        $this->xssExcept[] = $field;
        return $this;
    }

    /**
     * @see Binder::execute()
     * @return object|array
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
            $binder = Binder::create($this->xssSecure)->bind($this->bind)->to($this->to);

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

            foreach ($this->xssExcept as $xssExcept) {
                $binder->xssExcept($xssExcept);
            }

            if (is_object($this->bind)) {
                $reflection = new \ReflectionObject($this->bind);

                foreach ($reflection->getMethods() as $method) {
                    if ($this->isGetter($method)) {
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

    private function isAssocArray($value)
    {
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
