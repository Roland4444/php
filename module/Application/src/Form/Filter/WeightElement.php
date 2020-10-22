<?php

namespace Application\Form\Filter;

class WeightElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);

        $form->add([
            'name' => 'weightFrom',
            'attributes' => [
                'type' => 'number',
                'id' => 'weight_from',
                'placeholder' => 'тонн от',
                'class' => 'form-control',
                'value' => $params['weightFrom'],
                'step' => '0.001'
            ],

        ]);

        $form->add([
            'name' => 'weightTo',
            'attributes' => [
                'type' => 'number',
                'id' => 'weight_to',
                'placeholder' => 'тонн до',
                'class' => 'form-control',
                'value' => $params['weightTo'],
                'step' => '0.001'
            ],
        ]);

        return $form;
    }
}
