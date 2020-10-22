<?php
namespace Reference\Form;

use Core\Utils\Options;
use Reference\Entity\Customer;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Form\Element\Checkbox;

/**
 * Class CustomerForm
 * @package Reference\Form
 */
class CustomerForm extends AbstractReferenceForm
{

    public function __construct()
    {
        parent::__construct('customer');
        $this->setAttribute('method', 'post');
    }

    /**
     * @return mixed|void
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
            'type' => Checkbox::class,
            'name' => 'def',
            'options' => [
                'label' => 'По умолчанию',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => 'legal',
            'options' => [
                'label' => 'Юр. лицо',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => Checkbox::class,
            'name' => Options::ARCHIVE,
            'options' => [
                'label' => 'Поставщик в архиве',
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
     * @param Customer $object
     * @param int $flags
     * @return Form
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED): Form
    {
        if ($object->isArchive()) {
            $this->get(Options::ARCHIVE)->setValue(1);
        }
        return parent::bind($object, $flags);
    }
}
