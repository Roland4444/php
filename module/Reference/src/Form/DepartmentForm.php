<?php

namespace Reference\Form;

use Core\Utils\Options;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Department;
use Zend\Form\FormInterface;

/**
 * Class DepartmentForm
 * @package Reference\Form
 */
class DepartmentForm extends PlainForm
{
    protected $entityClass = Department::class;

    /**
     * DepartmentForm constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('user', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Department());
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
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'name' => 'alias',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Алиас:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'label' => 'Вид лома:',
                'value_options' => [
                    'black' => 'Черный',
                    'color' => 'Цветной'
                ],
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'source',
            'options' => [
                'label' => 'Источник металла:',
                'object_manager' => $this->entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'empty_option' => 'Нет',
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::HIDE,
            'options' => [
                'label' => 'Не показывать в общем меню',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::CLOSED,
            'options' => [
                'label' => 'Подразделение закрыто',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }

    /**
     * Собирает форму и наполняет ее данными из сущности
     *
     * @param Department $object
     * @param int $flags
     * @return \Zend\Form\Form
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isClosed()) {
            $this->get(Options::CLOSED)->setValue(1);
        }
        if ($object->isHide()) {
            $this->get(Options::HIDE)->setValue(1);
        }
        return parent::bind($object, $flags);
    }
}
