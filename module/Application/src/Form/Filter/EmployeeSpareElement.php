<?php

namespace Application\Form\Filter;

use Reference\Entity\Employee;

/**
 * Class EmployeeSpare
 * @package Application\Form\Filter
 */
class EmployeeSpareElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'employeeSpare',
            'options' => [
                'label' => 'Сотрудник:',
                'object_manager' => $this->entityManager,
                'target_class' => Employee::class,
                'property' => 'name',
                'empty_option' => 'Сотрудник',
                'find_method' => [
                    'name' => 'findConsumersOfSpares',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['employeeSpare'],
                'class' => 'form-control s2',
            ],
        ]);
        return $form;
    }
}
