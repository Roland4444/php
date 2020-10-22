<?php

namespace Application\Form\Filter;

/**
 * Class TypeElement
 * @package Application\Form\Filter
 */
class TypeElement extends AElement
{

    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'label' => 'Тип:',
                'value_options' => [
                    '' => 'Тип',
                    'black' => 'Черный',
                    'color' => 'Цветной'
                ],
            ],
            'attributes' => [
                'value' => isset($params['type']) ? $params['type'] : '',
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
