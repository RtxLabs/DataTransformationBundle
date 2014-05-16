<?php
namespace RtxLabs\DataTransformationBundle\Binder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\DBAL\Types\Type;

class DoctrineBinder implements IBinder {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $bind;
    private $to;
    private $fields = array();
    private $except = array();
    private $joins = array();
    private $metadata = null;
    private $whitelisting;
    private $xssSecure;
    private $xssExcept = array();

    /**
     * @param $em \Doctrine\ORM\EntityManager
     * @param boolean $whitelisting if whitelisting is true, the fields that has to be bound has to be defined by
     *                              calling the "field" method.
     * @param boolean $xssSecure  if xssSecure is true, some html chars will be removed from strings.
     * @return DoctrineBinder
     */
    public function __construct($em, $whitelisting=true, $xssSecure=true)
    {
        $this->xssSecure = $xssSecure;
        $this->whitelisting = $whitelisting;
        $this->em = $em;
    }

    /**
     * @static
     * @return \RtxLabs\DataTransformationBundle\Binder\DoctrineBinder
     */
    public static function create($em, $whitelisting=true, $xssSecure=true)
    {
        return new self($em, $whitelisting, $xssSecure);
    }

    /**
     * @see Binder::bind()
     *
     * @param mixed $entity the entity or array to bind
     * @return DoctrineBinder
     */
    public function bind($object)
    {
        $this->bind = $object;
        return $this;
    }

    /**
     * @see Binder::to()
     *
     * @param $object
     * @return DoctrineBinder
     */
    public function to($object)
    {
        $this->to = $object;
        return $this;
    }

    /**
     * @see Binder::field()
     *
     * @param string $field
     * @param closure $closure
     * @return DoctrineBinder
     */
    public function field($field, $value=null) {
        $this->fields[$field] = $value;
        return $this;
    }

    /**
     * The given binder will be bound and executed on every value of the given field.
     *
     * $binder->join("users", Binder::create()->field("username"));
     *
     * @param $field
     * @param IBinder $binder
     * @return DoctrineBinder
     */
    public function join($field, IBinder $binder) {
        $this->joins[$field] = $binder;
        return $this;
    }

    /**
     * If the DoctrineBinder was created without whitelisting and some values should not be bound,
     * except can be used.
     *
     * @param string $field the field that should not be bound
     * @return DoctrineBinder
     */
    public function except($field) {
        $this->except[] = $field;
        return $this;
    }

    /**
     * @see Binder::execute()
     *
     * @return mixed
     */
    public function execute()
    {
        if ($this->bind === null) {
            return null;
        }

        $getMethodBinder = GetMethodBinder::create($this->whitelisting)->bind($this->bind)->to($this->to);

        if ($this->bind instanceof \Doctrine\Common\Collections\Collection) {
            $getMethodBinder->bind($this->bind->toArray());
        }

        if ($this->to != null) {
            $modifiedBind = array();

            $reflection = new \ReflectionObject($this->to);
            $metaData = $this->em->getClassMetadata($reflection->getName());

            foreach ($this->bind as $field=>$value) {
                if ($this->whitelisting && !array_key_exists($field, $this->fields)) {
                    $modifiedBind[$field] = $value;
                }
                elseif ($value === null) {
                    $modifiedBind[$field] = null;
                }
                elseif ($this->isDateTime($field, $metaData)) {
                    $modifiedBind[$field] = $this->getDateTime($value);
                }
                elseif ($metaData->isSingleValuedAssociation($field)) {
                    $modifiedBind[$field] = $this->getReference($value, $field, $metaData);
                }
                else {
                    $modifiedBind[$field] = $value;
                }
            }

            $getMethodBinder->bind($modifiedBind);
        }

        if (is_object($this->bind)
                && !($this->bind instanceof \stdClass)
                && !($this->bind instanceof \Doctrine\Common\Collections\Collection)) {

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

        foreach ($this->xssExcept as $xssExcept) {
            $getMethodBinder->xssExcept($xssExcept);
        }

        return $getMethodBinder->execute();
    }

    private function getReference($value, $field, $metaData)
    {
        $id = $value;
        if ($value instanceof \stdClass) {
            $id = $value->id;
        }

        $id = intval($id);

        if ($id < 1) {
            return null;
        }

        $reference = $this->em->getReference($metaData->getAssociationTargetClass($field), $id);
        return $reference;
    }

    private function getDateTime($value)
    {
        if ($value < 1) {
            $date = null;
            return $date;
        } else {
            $date = new \DateTime();
            $date->setTimestamp($value);
            return $date;
        }
    }

    private function isDateTime($field, ClassMetadata $metaData)
    {
        $fieldType = $metaData->getTypeOfField($field);

        return $fieldType == Type::DATETIME
            || $fieldType == Type::DATE
            || $fieldType == Type::TIME;
    }

    /**
     * If the DoctrineBinder was created with xssSecure and some values should not be cleaned,
     * xssExcept can be used.
     *
     * @param string $field the field that should not be cleaned
     * @return DoctrineBinder
     */
    public function xssExcept($field) {
        $this->xssExcept[] = $field;
        return $this;
    }
}
