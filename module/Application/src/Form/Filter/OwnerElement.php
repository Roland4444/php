<?php

namespace Application\Form\Filter;

use Reference\Entity\ContainerOwner;

/**
 * Class OwnerElement
 * @package Application\Form\Filter
 */
class OwnerElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'owner',
            'options' => [
                'label' => 'Собственник:',
                'object_manager' => $this->entityManager,
                'target_class' => ContainerOwner::class,
                'property' => 'name',
                'empty_option' => 'Выбрать собственника',
            ],
            'attributes' => [
                'required' => false,
                'value' => $params['owner'],
                'id' => 'owner',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
