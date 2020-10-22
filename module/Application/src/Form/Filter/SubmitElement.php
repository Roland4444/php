<?php

namespace Application\Form\Filter;

/**
 * Class SubmitElement
 * @package Application\Form\Filter\
 */
class SubmitElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);

        $form->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Фильтр',
                'id' => 'submitbutton',
            ],
        ]);

        return $form;
    }
}
