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

    /**
     * @static
     * @return \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    public static function create()
    {
        return new self();
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
    public function field($field, $closure) {
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
        //todo: move array_values(...) to rotex lib
        $result = array();
        $binder = Binder::create();

        foreach ($this->joins as $field => $joinedBinder) {
            $binder->join($field, $joinedBinder);
        }

        if (is_null($this->bind)) {
            return null;
        }
        elseif (is_array($this->bind) && array_values($this->bind) === $this->bind) {
            foreach ($this->bind as $item) {
                $result[] = $this->copy()->bind($item)->execute();
            }
        }
        elseif (is_array($this->bind) && array_values($this->bind) !== $this->bind) {
            $binder->bind($this->bind)->to($this->to);

            foreach ($this->fields as $key=>$value) {
                $binder->field($key, $value);
            }

            foreach ($this->bind as $key=>$value) {
                $binder->field($key, $value);
            }

            $result = $binder->execute();
        }
        elseif (is_object($this->bind)) {
            $binder->bind($this->bind)->to($this->to);

            foreach ($this->fields as $field=>$closure) {
                $binder->field($field, $closure);
            }

            $reflection = new \ReflectionObject($this->bind);

            foreach ($reflection->getMethods() as $method) {
                if ($this->isGetter($method)
                    && !$this->methodReturnsSymfonyCollection($method)) {

                    $fieldName = lcfirst(substr($method->getName(), 3));

                    if (!in_array($fieldName, $this->except)) {
                        $binder->field($fieldName);
                    }
                }
            }

            foreach ($reflection->getProperties() as $property) {
                if ($property->isPublic() && !in_array($property->getName(), $this->except)) {
                    $binder->field($property->getName());
                }
            }

            $binder->bind($this->bind);

            $result = $binder->execute();
        }

        return $result;
    }

    /**
     * @param \ReflectionMethod $method
     * @return boolean
     */
    private function methodReturnsSymfonyCollection($method)
    {
        if (strpos($method->getDocComment(), '@return Doctrine\Common\Collections\Collection') !== false) {
            return true;
        }
        else {
            return false;
        }
    }

    private function copy() {
        $copy = new GetMethodBinder();
        $copy->bind = $this->bind;
        $copy->fields = $this->fields;
        $copy->to = $this->to;
        $copy->except = $this->except;

        return $copy;
    }

    private function isGetter($method)
    {
        $result = true;

        if (substr($method->getName(), 0, 3) != "get") {

            $result = false;
        }

        if (!$method->isPublic()) {
            $result = false;
        }

        return $result;
    }
}
