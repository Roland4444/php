<?php
namespace Reference\Entity;

use Core\Entity\EntityWithOptions;
use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Reference\Repositories\WarehouseRepository")
 * @ORM\Table(name="warehouse")
 */
class Warehouse
{
    use EntityWithOptions;

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="json") */
    private $options;

    /**
     * @ORM\ManyToMany(targetEntity="Reference\Entity\User")
     * @ORM\JoinTable(name="warehouse_user_ref",
     *      joinColumns={@ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    private $users;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return mb_strtoupper($this->name);
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $users
     */
    public function addUsers($users)
    {
        $this->users->add($users);
    }

    public function clearUsers()
    {
        $this->users->clear();
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Проверяет, закрыт ли хоз. склад
     *
     * @return bool
     */
    public function isClosed()
    {
        return $this->hasOption(Options::CLOSED);
    }
}
