<?php

namespace Finance\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Finance\Entity\Template;
use Zend\Form\Form;
use Reference\Entity\Category;
use DoctrineORMModule\Form\Element\EntitySelect;

class TemplateForm extends Form
{
    public function __construct($em)
    {
        parent::__construct('template');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($em))->setObject(new Template());

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'category',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $em,
                'target_class' => Category::class,
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'inn',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
        ]);

        $this->add([
            'name' => 'text',
            'attributes' => [
                'type' => 'text',
                'id' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Текст:',
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
