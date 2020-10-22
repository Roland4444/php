<?php

namespace Application\Form\Filter;

/**
 * Class AElement
 *
 * @package Application\Form\Filter\
 */
abstract class AElement implements IElement
{
    use BaseFilterForm;

    protected $element;
    protected $entityManager;

    /**
     * AElement constructor.
     *
     * @param IElement $element
     */
    public function __construct(IElement $element)
    {
        $this->element = $element;
        $this->entityManager = $this->element->getEntityManager();
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getForm(array $params)
    {
        return $this->element->getForm($params);
    }
}
