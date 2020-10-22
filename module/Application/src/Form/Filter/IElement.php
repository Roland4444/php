<?php
namespace Application\Form\Filter;

/**
 * Interface IElement
 * @package Application\Form\Filter
 */
interface IElement
{
    /**
     * Get form
     * @param array $params
     * @return mixed
     */
    public function getForm(array $params);

    /**
     * Get entity manager
     * @return mixed
     */
    public function getEntityManager();
}
