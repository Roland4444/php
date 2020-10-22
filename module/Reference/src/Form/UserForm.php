<?php
namespace Reference\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Department;
use Reference\Entity\Role;
use Reference\Entity\User;

/**
 * UserForm User
 * @package Reference\Form
 */
class UserForm extends PlainForm
{
    protected $entityClass = User::class;

    /**
     * UserForm constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('user', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new User());
    }

    /**
     * {@inheritdoc}
     */
    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => 'Имя пользователя'
            ],
            'options' => [
                'label' => 'Имя:',
            ],
        ]);

        $this->add([
            'name' => 'login',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
                'placeholder' => 'Логин пользователя'
            ],
            'options' => [
                'label' => 'Логин:',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'type' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Пароль'
            ],
            'options' => [
                'label' => 'Пароль:',
            ],
        ]);

        $this->add([
            'name' => 'confirm_password',
            'attributes' => [
                'type' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Повторите пароль'
            ],
            'options' => [
                'label' => 'Повторите пароль:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntityMultiCheckbox',
            'name' => 'roles',
            'options' => [
                'label' => 'Роли:',
                'object_manager' => $this->entityManager,
                'target_class' => Role::class,
                'property' => 'name',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $this->entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'empty_option'   => 'Привязать к подразделению',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => true,
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'change_password',
            'options' => [
                'label' => 'Попросить сменить пароль',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'is_blocked',
            'options' => [
                'label' => 'Заблокирован',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'login_from_internet',
            'options' => [
                'label' => 'Разрешен вход из сети',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
