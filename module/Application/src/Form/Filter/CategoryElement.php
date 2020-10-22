<?php

namespace Application\Form\Filter;

use Reference\Entity\Category;
use DoctrineORMModule\Form\Element\EntitySelect;

/**
 * Class CategoryElement
 * @package Application\Form\Filter
 */
class CategoryElement extends AElement
{
    private array $roles;

    /**
     * CategoryElement constructor.
     * @param IElement $element
     * @param []int $roles
     */
    public function __construct(IElement $element, $roles)
    {
        parent::__construct($element);
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => EntitySelect::class,
            'name' => 'category',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $this->entityManager,
                'target_class' => Category::class,
                'property' => 'name',
                'empty_option' => 'Выбрать категорию',
                'find_method' => [
                    'name' => 'findByRole',
                    'params' => [
                        'roles' => $this->roles,
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => $params['category'] ?? '',
                'class' => 'form-control s2',
            ]
        ]);
        return $form;
    }
}
