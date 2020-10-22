<?php

namespace Application\Form\Filter;

use Core\Utils\Options;

class VehicleTypeElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'movable',
            'options' => [
                'label' => 'Тип:',
                'object_manager' => $this->entityManager,
                'target_class' => 'Reference\Entity\Vehicle',
                'value_options' => [
                    '' => 'Тип',
                    Options::MOVABLE => 'Передвижная',
                    Options::NONMOVABLE => 'Не передвижная',
                ],
            ],
            'attributes' => [
                'value' => $params['movable'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
