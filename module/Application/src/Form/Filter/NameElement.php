<?php
namespace Application\Form\Filter;

use Zend\Form\Form;

/**
 * Class NametElement
 * @package Application\Form\Filter
 */
class NameElement implements IElement
{

    private $entityManager;

    /**
     * DateElement constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
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
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'value' => isset($params['name']) ? $params['name'] : '',
                'placeholder' => 'Наименование',
                'class' => 'form-control',
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);
        return $form;
    }
}
