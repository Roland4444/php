<?php

namespace Finance\Controller;

use Finance\Entity\BankAccount;
use Finance\Form\BankForm;
use Finance\Service\BankService;
use Reference\Controller\PlainReferenceController;
use Zend\View\Model\ViewModel;

/**
 * Class BankController
 * @package Finance\Controller
 */
class BankController extends PlainReferenceController
{
    protected $routeIndex = 'bank';

    /**
     * BankController constructor.
     * @param $service
     * @param $form
     */
    public function __construct(BankService $service, BankForm $form)
    {
        $this->service = $service;
        $this->form = $form;
        $this->entityInstance = new BankAccount();
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
        $rows = $this->service->findAll(true);

        return new ViewModel([
            'rows' => $rows,
        ]);
    }
}
