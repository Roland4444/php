<?php
namespace Spare\Form;

use Reference\Form\AbstractReferenceForm;

/**
 * Class SpareForm
 * @package Reference\Form
 */
class SpareForm extends AbstractReferenceForm
{

    /**
     * SpareForm constructor.
     */
    public function __construct()
    {
        parent::__construct('spares');
        $this->setAttribute('method', 'post');
    }

    /**
     * Add elements
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
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Комментарий:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'isComposite',
            'options' => [
                'label' => 'Составное - может быть разделено на несколько единиц (литров/шт и т.п.)',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'name' => 'units',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Ед. изм.:',
            ],
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
     * {@inheritDoc}
     */
    public function setData($data): void
    {
        $data['isComposite'] = $data['isComposite'] == 'true' ? 1 : 0;
        parent::setData($data);
    }
}
