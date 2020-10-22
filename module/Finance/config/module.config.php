<?php

namespace Finance;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'bank' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/bank[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\BankController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'cashTransfer' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/cash-transfer[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CashTransferController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'moneyToDepartment' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/money-department[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\MoneyToDepartmentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'mainOtherExpense' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/other-expense[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OtherExpenseController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'financeImport' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/finance/import[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TemplateController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'main_metal_expenses' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/finance/metal-expense[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\MetalExpenseController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'otherReceipts' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/other-receipts[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OtherReceiptsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'traderReceipts' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/trader-receipts[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TraderReceiptsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'mainTotal' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/main/total[/clear/:clear][/:action]',
                    'constraints' => [
                        'action' => 'json',
                    ],
                    'defaults' => [
                        'controller' => Controller\TotalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_bank_expense' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office/bank[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeOtherExpenseController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_bank_total' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office/total[/:action]',
                    'constraints' => [
                        'action' => 'json',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeCashBankTotalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_cash_transfer' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office/cash-transfer[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeCashTransferController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_other_receipts' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office/other-receipts[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeOtherReceiptsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'office_trader_receipts' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/office/trader_receipts[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeTraderReceiptsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\BankController::class => Controller\Factory\BankControllerFactory::class,
            Controller\CashTransferController::class => Controller\Factory\CashTransferControllerFactory::class,
            Controller\OfficeCashTransferController::class => Controller\Factory\OfficeCashTransferControllerFactory::class,
            Controller\MoneyToDepartmentController::class => Controller\Factory\MoneyToDepartmentControllerFactory::class,
            Controller\OtherExpenseController::class => Controller\Factory\OtherExpenseControllerFactory::class,
            Controller\OfficeOtherExpenseController::class => Controller\Factory\OfficeOtherExpenseControllerFactory::class,
            Controller\TraderReceiptsController::class => Controller\Factory\TraderReceiptsControllerFactory::class,
            Controller\OfficeTraderReceiptsController::class => Controller\Factory\OfficeTraderReceiptsControllerFactory::class,
            Controller\TotalController::class => Controller\Factory\TotalControllerFactory::class,
            Controller\OfficeCashBankTotalController::class => Controller\Factory\OfficeCashBankTotalControllerFactory::class,
            Controller\OtherReceiptsController::class => Controller\Factory\OtherReceiptsControllerFactory::class,
            Controller\OfficeOtherReceiptsController::class => Controller\Factory\OfficeOtherReceiptsControllerFactory::class,
            Controller\MetalExpenseController::class => Controller\Factory\MetalExpenseControllerFactory::class,
            Controller\TemplateController::class => Controller\Factory\TemplateControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Facade\Finance::class => Facade\FinanceFactory::class,
            Service\BankService::class => Service\Factory\BankAccountServiceFactory::class,
            Form\BankForm::class => Form\Factory\BankFormFactory::class,
            'cashTransferService'      => Service\CashTransferService::class,
            Service\MoneyToDepartmentService::class => Service\Factory\MoneyToDepartmentServiceFactory::class,
            'financeOtherExpenseService' => Service\OtherExpenseService::class,
            Service\OtherReceiptService::class     => Service\OtherReceiptService::class,
            'traderReceiptsService'    => Service\TraderReceiptsService::class,
            'financePaymentTemplateService'    => Service\TemplateService::class,
            'financeTotalService'      => Service\TotalServiceFactory::class,
            'bankClientService' => Service\Factory\BankClientServiceFactory::class,
            'financeMetalExpense' => Service\Factory\MetalExpenseServiceFactory::class,
            //Dao
            'financeTotalDao' => Dao\TotalServiceDaoFactory::class
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'Application_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
