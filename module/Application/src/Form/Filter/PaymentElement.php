<?php

namespace Application\Form\Filter;

/**
 * Class DiamondElement
 * @package Application\Form\Filter
 */
class PaymentElement extends AElement
{

    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'payment',
            'options' => [
                'label' => 'Тип:',
                'value_options' => [
                    '' => 'Оплата',
                    'cash' => 'Нал',
                    'formal' => 'Безнал',
                    'diamond' => 'Карта'
                ],
            ],
            'attributes' => [
                'value' => isset($params['payment']) ? $params['payment'] : '',
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
