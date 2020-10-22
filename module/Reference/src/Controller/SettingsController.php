<?php

namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Reference\Form\SettingsForm;
use Reference\Service\SettingsService;

/**
 * Class SettingsController
 * @package Reference\Controller
 */
class SettingsController extends PlainReferenceController
{
    use FilterableController;

    protected $routeIndex = "settings";

    /**
     * SettingsController constructor.
     * @param SettingsService $service
     * @param SettingsForm $form
     */
    public function __construct(SettingsService $service, SettingsForm $form)
    {
        $this->service = $service;
        $this->form = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function addAction()
    {
        return $this->redirect()->toRoute($this->routeIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction()
    {
        return $this->redirect()->toRoute($this->routeIndex);
    }
}
