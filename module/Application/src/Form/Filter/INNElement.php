<?php
namespace Application\Form\Filter;

class INNElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'name' => 'inn',
            'attributes' => [
                'type' => 'text',
                'value' => isset($params['inn']) ? $params['inn'] : '',
                'placeholder' => 'ИНН',
                'class' => 'form-control',
                'id' => 'inn'
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
        ]);
        return $form;
    }
}
