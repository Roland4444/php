<?php

namespace Application\Form\Filter;

/**
 * Class NumberElement
 * @package Application\Form\Filter
 */
class NumberElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'name' => 'number',
            'attributes' => [
                'type' => 'number',
                'id' => 'name',
                'class' => 'form-control',
                'placeholder' => 'Номер',
                'min' => 1,
                'value' => $params['number'],
            ],
            'options' => [
                'label' => 'Номер заказа:',
            ],
        ]);

        return $form;
    }
}
