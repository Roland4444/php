<?php

namespace Application\Form\Filter;

use Reference\Entity\Department;

/**
 * Class DepartmentElement
 * @package Application\Form\Filter
 */
class DepartmentElement extends AElement
{
    private $isAdmin;

    /**
     * {@inheritdoc}
     */
    public function __construct(IElement $element, $isAdmin = false)
    {
        parent::__construct($element);
        $this->isAdmin = $isAdmin;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $this->entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'empty_option' => 'Выбрать подразделение',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => $this->isAdmin,
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['department']) ? $params['department'] : '',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
