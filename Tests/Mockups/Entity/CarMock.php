<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\CarMock
 *
 * @ORM\Table(name="core_test_car_mock")
 * @ORM\Entity(repositoryClass="RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\CarMockRepository")
 */
class CarMock
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name = "";

    /**
     * Get id
     *
     * @return integer
     */
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
