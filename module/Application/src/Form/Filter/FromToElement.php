<?php

namespace Application\Form\Filter;

use Reference\Entity\Department;

/**
 * Class FromToElement
 * @package Application\Form\Filter
 */
class FromToElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'source',
            'options' => [
                'label' => 'Источник:',
                'object_manager' => $this->entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'empty_option' => 'Выбрать источник',
            ],
            'attributes' => [
                'required' => false,
                'value' => $params['source'],
                'class' => 'form-control',
            ]
        ]);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'dest',
            'options' => [
                'label' => 'Получатель:',
                'object_manager' => $this->entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'empty_option' => 'Выбрать получателя',
            ],
            'attributes' => [
                'required' => false,
                'value' => $params['dest'],
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
