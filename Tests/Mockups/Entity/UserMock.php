<?php
namespace RtxLabs\DataTransformationBundle\Tests\Mockups\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMock
 *
 * @ORM\Table(name="user_mock")
 * @ORM\Entity(repositoryClass="RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMockRepository")
 */
class UserMock
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
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var \DateTime
     *
     * @ORM\column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMock
     * @ORM\ManyToOne(targetEntity="UserMock")
     * @ORM\JoinColumn(name="deletedBy_id", referencedColumnName="id")
     */
    protected $deletedBy;

    /**
     * @var \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\CarMock
     *
     * @ORM\ManyToOne(targetEntity="CarMock")
     */
    private $car;

    /**
     * @ORM\ManyToMany(targetEntity="UserMock")
     * @ORM\JoinTable(name="user_group",
     *          joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $groups;

    public static function create($name) {
        $user = new self();
        $user->setUsername($name);

        return $user;
    }

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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        assert(is_string($username));
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set deletedAt
     *
     * @param datetime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get deletedAt
     *
     * @return datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\CarMock
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\CarMock $car
     */
    public function setCar($car)
    {
        $this->car = $car;
    }

    /**
     * Set deletedBy
     *
     * @param \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMock $deletedBy
     */
    public function setDeletedBy($deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * Get deletedBy
     *
     * @return \RtxLabs\DataTransformationBundle\Tests\Mockups\Entity\UserMock
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    public function addGroup(GroupMock $groups)
    {
        $this->groups[] = $groups;
    }

    public function getGroups()
    {
        return $this->groups;
    }
}
