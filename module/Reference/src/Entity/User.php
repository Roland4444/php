<?php

namespace Reference\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string") */
    protected $login;

    /** @ORM\Column(type="string") */
    private $password;

    /** @ORM\Column(type="string") */
    private $pass;

    /**
     * @ORM\ManyToMany(targetEntity="Reference\Entity\Role")
     * @ORM\JoinTable(name="user_role_ref",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     */
    private $department;

    /** @ORM\Column(name="`change_password`", type="integer") */
    private $change_password;

    /** @ORM\Column(name="`is_blocked`", type="integer") */
    private $is_blocked;

    /** @ORM\Column(name="`login_from_internet`", type="integer") */
    private $login_from_internet;

    /** @ORM\Column(type="integer") */
    private $attempts;

    /** @ORM\Column(type="string") */
    private $token;

    /** @ORM\Column(name="`token_expired`", type="string") */
    private $tokenExpired;

    /**
     * @ORM\ManyToMany(targetEntity="Reference\Entity\Warehouse")
     * @ORM\JoinTable(name="warehouse_user_ref",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")}
     *      )
     */
    private $warehouses;

    public function __construct()
    {
        $this->warehouses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getWarehouses()
    {
        return $this->warehouses;
    }

    public function isAdmin()
    {
        return in_array(Role::ROLE_ADMIN, $this->getRoleNames());
    }

    public function isGlavbuh()
    {
        return in_array(Role::ROLE_GLAVBUH, $this->getRoleNames());
    }

    public function isOfficeCash()
    {
        return in_array(Role::ROLE_CASH, $this->getRoleNames());
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
        return $this->name;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setPassword($password)
    {
        if (! empty($password)) {
            $this->password = $password;
        }
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPass($pass)
    {
        if (! empty($pass)) {
            $this->pass = $pass;
        }
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function setChangePassword($change_password)
    {
        $this->change_password = $change_password;
    }

    public function getChangePassword()
    {
        return $this->change_password;
    }

    public function setIsBlocked($is_blocked)
    {
        $this->is_blocked = $is_blocked;
    }

    public function getIsBlocked()
    {
        return $this->is_blocked;
    }

    public function setLoginFromInternet($login_from_internet)
    {
        $this->login_from_internet = $login_from_internet;
    }

    public function getLoginFromInternet()
    {
        return $this->login_from_internet;
    }

    /**
     * @param mixed $attempts
     * @return User
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttempts()
    {
        return $this->attempts;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getTokenExpired()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->tokenExpired);
    }

    /**
     * @return array
     */
    public function getRoleNames(): array
    {
        $rolesEnum = [];

        /** @var Role $role */
        foreach ($this->roles as $role) {
            $rolesEnum[] = $role->getName();
        }

        return $rolesEnum;
    }

    /**
     * @return array
     */
    public function getRoleIds(): array
    {
        $rolesEnum = [];

        /** @var Role $role */
        foreach ($this->roles as $role) {
            $rolesEnum[] = $role->getId();
        }

        return $rolesEnum;
    }

    /**
     * @param mixed $tokenExpired
     */
    public function setTokenExpired($tokenExpired): void
    {
        $this->tokenExpired = $tokenExpired;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'name',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 40,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'login',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 40,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[_\.\-a-zA-Z0-9]+$/',
                            'messages' => [
                                \Zend\Validator\Regex::INVALID => 'Invalid input, only 0-9, characters a-z and symbol "_", ".", "-"',
                                \Zend\Validator\Regex::NOT_MATCH => 'Invalid input, only 0-9, characters a-z and symbol "_", ".", "-"',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 128,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Z][a-zA-Z0-9_!@#$%^&*().]{8,25}$/',
                            'messages' => [
                                \Zend\Validator\Regex::INVALID => 'Пароль должен сождержать большую букву, маленькую букву, цифру и быть не короче 8 символов',
                                \Zend\Validator\Regex::NOT_MATCH => 'Пароль должен сождержать большую букву, маленькую букву, цифру и быть не короче 8 символов',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'confirm_password',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => ['min' => 6],
                    ],
                    [
                        'name' => 'identical',
                        'options' => ['token' => 'password']
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'department',
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function addRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $this->roles->add($role);
        }
    }

    public function removeRoles(Collection $roles)
    {
        foreach ($roles as $role) {
            $this->roles->removeElement($role);
        }
    }
}
