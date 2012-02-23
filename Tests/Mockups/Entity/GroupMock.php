<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\GroupMock
 *
 * @ORM\Table(name="core_test_user_mock")
 * @ORM\Entity(repositoryClass="RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\GroupMockRepository")
 */
class GroupMock
{
    private static $COUNT = 1;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $username
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name = "";

    public static function create() {
        $group = new self();
        $group->setName("name"+self::$COUNT);

        self::$COUNT++;

        return $group;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
