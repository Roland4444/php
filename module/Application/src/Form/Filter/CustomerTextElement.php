<?php

namespace Application\Form\Filter;

/**
 * Class CustomerTextElement
 * @package Application\Form\Filter
 */
class CustomerTextElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'name' => 'customerText',
            'attributes' => [
                'type' => 'text',
                'value' => isset($params['customerText']) ? $params['customerText'] : '',
                'placeholder' => 'Поставщик',
                'class' => 'form-control',
                'id' => 'customerText'
            ],
            'options' => [
                'label' => 'Поставщик:',
            ],
        ]);
        return $form;
    }
}
