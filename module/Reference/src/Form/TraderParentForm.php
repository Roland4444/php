<?php
namespace Reference\Form;

use Core\Utils\Options;
use Reference\Entity\TraderParent;
use Zend\Form\FormInterface;

class TraderParentForm extends AbstractReferenceForm
{

    public function __construct()
    {
        parent::__construct('trader_parent');
        $this->setAttribute('method', 'post');
    }

    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'name' => 'order',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Порядок отображения:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::HIDE,
            'options' => [
                'label' => 'Скрывать',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }

    /**
     * Собирает форму и наполняет ее данными из сущности
     *
     * @param TraderParent $object
     * @param int $flags
     * @return \Zend\Form\Form
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isHide()) {
            $this->get(Options::HIDE)->setValue(1);
        }
        return parent::bind($object, $flags);
    }
}
