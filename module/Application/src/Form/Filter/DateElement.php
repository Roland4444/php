<?php
namespace Application\Form\Filter;

use Zend\Form\Form;

/**
 * Class DateElement
 * @package Application\Form\Filter
 */
class DateElement implements IElement
{

    private $entityManager;

    /**
     * DateElement constructor.
     * @param $entityManager
     */
    public function __construct($entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = new Form('filter');
        $form->setAttribute('method', 'post');

        $form->add([
            'name' => 'startdate',
            'attributes' => [
                'type' => 'text',
                'id' => 'startdate',
                'placeholder' => 'Начало периода',
                'value' => $params['startdate'],
                'class' => 'form-control ui-date',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $form->add([
            'name' => 'enddate',
            'attributes' => [
                'type' => 'text',
                'id' => 'enddate',
                'placeholder' => 'Конец периода',
                'value' => $params['enddate'],
                'class' => 'form-control ui-date',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);
        return $form;
    }
}
