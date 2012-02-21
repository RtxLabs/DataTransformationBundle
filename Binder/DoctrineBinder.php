<?php
namespace RtxLabs\DataTransformationBundle\Binder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\PersistentCollection;

class DoctrineBinder implements IBinder {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \RtxLabs\DataTransformationBundle\Binder\GetMethodBinder
     */
    private $binder;

    private $bind;
    private $to;
    private $fields = array();
    private $except = array();
    private $joins = array();

    /**
     * @param $em \Doctrine\ORM\EntityManager
     */
    private function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @static
     * @return \RtxLabs\DataTransformationBundle\Binder\DoctrineBinder
     */
    public static function create($em)
    {
        return new self($em);
    }

    public function bind($object)
    {
        $this->bind = $object;
        return $this;
    }

    public function to($object)
    {
        $this->to = $object;
        return $this;
    }

    public function field($field, $value) {
        $this->fields[$field] = $value;
        return $this;
    }

    public function join($field, IBinder $binder) {
        $this->joins[$field] = $binder;
        return $this;
    }

    public function except($field) {
        $this->except[] = $field;
        return $this;
    }

    /**
     * @return object
     */
    public function execute()
    {
        if ($this->bind == null) {
            return null;
        }

        $getMethodBinder = GetMethodBinder::create()->bind($this->bind)->to($this->to);

        if ($this->bind instanceof PersistentCollection) {
            $getMethodBinder->bind($this->bind->toArray());
        }

        if ($this->to != null) {
            $modifiedBind = array();

            $reflection = new \ReflectionObject($this->to);

            foreach ($this->bind as $field=>$value) {
                $metaData = $this->em->getClassMetadata($reflection->getName());
                $fieldType = $metaData->getTypeOfField($field);

                if ($value != null && $fieldType == Type::DATETIME || $fieldType == Type::DATE || $fieldType == Type::TIME) {
                    $date = new \DateTime();
                    $date->setTimestamp($value);
                    $modifiedBind[$field] = $date;
                }
                elseif ($value != null && $metaData->isSingleValuedAssociation($field)) {
                    $modifiedBind[$field] = $this->em->getReference($metaData->getAssociationTargetClass($field), $value);
                }
                else {
                    $modifiedBind[$field] = $value;
                }
            }

            $getMethodBinder->bind($modifiedBind);
        }

        if (is_object($this->bind) && !($this->bind instanceof PersistentCollection)) {
            $reflection = new \ReflectionObject($this->bind);
            $metaData = $this->em->getClassMetadata($reflection->getName());

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (substr($method->getName(), 0, 3) == "get") {
                    $fieldName = lcfirst(substr($method->getName(), 3));

                    if ($metaData->isCollectionValuedAssociation($fieldName)) {
                        $getMethodBinder->except($fieldName);
                    }
                }
            }
        }

        foreach ($this->fields as $field => $value) {
            $getMethodBinder->field($field, $value);
        }

        foreach ($this->joins as $field => $binder) {
            $getMethodBinder->join($field, $binder);
        }

        foreach ($this->except as $except) {
            $getMethodBinder->except($except);
        }

        return $getMethodBinder->execute();
    }
}
