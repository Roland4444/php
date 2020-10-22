<?php
namespace Application\Form\Filter;

/**
 * Class QRElement
 * @package Application\Form\Filter
 */
class QRElement extends AElement
{

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'name' => 'qr',
            'attributes' => [
                'type' => 'text',
                'value' => isset($params['qr']) ? $params['qr'] : '',
                'placeholder' => 'QR код',
                'class' => 'form-control',
                'id' => 'qr',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'QR код:',
            ],
        ]);
        return $form;
    }
}
