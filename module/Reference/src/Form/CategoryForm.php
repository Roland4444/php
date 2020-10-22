<?php

namespace Reference\Form;

use Core\Utils\Options;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Category;
use Reference\Entity\CategoryGroup;
use Reference\Entity\Role;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilter;

/**
 * Class CategoryForm
 * @package Reference\Form
 */
class CategoryForm extends PlainForm
{
    protected $entityClass = Category::class;
    /**
     * CategoryForm constructor.
     * @param $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('category', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Category());
    }

    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
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
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'group',
            'options' => [
                'label' => 'Группа:',
                'object_manager' => $this->entityManager,
                'target_class' => CategoryGroup::class,
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'def',
            'options' => [
                'label' => 'По умолчанию:',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'archival',
            'options' => [
                'label' => 'Архивная:',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
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
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
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
                            'min' => 1,
                            'max' => 40,
                        ],
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
            $inputFilter->add([
                'name' => 'def',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'archival',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'group',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'alias',
                'required' => false,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isDefault()) {
            $this->get(Options::DEFAULT)->setValue(1);
        }
        if ($object->isArchived()) {
            $this->get(Options::ARCHIVAL)->setValue(1);
        }
        return parent::bind($object, $flags);
    }
}
