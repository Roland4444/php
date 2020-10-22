<?php

namespace Spare\Form;

use Reference\Form\AbstractReferenceForm;
use Zend\Form\FormInterface;

class SellerForm extends AbstractReferenceForm
{
    public function __construct($entityManager)
    {
        parent::__construct('user', $entityManager);
        $this->setAttribute('method', 'post');
    }

    /**
     * {@inheritdoc}
     */
    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Имя поставщика:',
            ],
        ]);

        $this->add([
            'name' => 'inn',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
        ]);

        $this->add([
            'name' => 'contacts',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Контакты:',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'textarea',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Комментарий:',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
