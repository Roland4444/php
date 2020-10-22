<?php

namespace Storage;

use Application\Controller\ControllerFactory;
use Storage\Controller\Factory\BalanceControllerFactory;
use Storage\Controller\Factory\CashInControllerFactory;
use Storage\Controller\Factory\CashTotalControllerFactory;
use Storage\Controller\Factory\ContainerControllerFactory;
use Storage\Controller\Factory\MetalExpenseControllerFactory;
use Storage\Controller\Factory\PurchaseControllerFactory;
use Storage\Controller\Factory\ShipmentControllerFactory;
use Storage\Controller\Factory\TransferControllerFactory;
use Zend\Router\Http\Literal;

return [
    'router' => [
        'routes' => [
            'purchase' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/purchase[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\PurchaseController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'purchaseNonFerrous' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/storage/purchase-non-ferrous[/clear/:clear]',
                    'constraints' => [
                    ],
                    'defaults' => [
                        'controller' => Controller\PurchaseNonFerrousController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'purchase_deal' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/purchase-deal[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\PurchaseDealController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'shipment' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/shipment[/clear/:clear][/:action][/:id][/:container]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShipmentController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'storage_container' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/container[/:action[/:id]][/:date]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ContainerController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'storage_container_item' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/container-item[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ContainerItemController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'balance' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/balance[/clear/:clear]',
                    'constraints' => [
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\BalanceController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'transfer' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/transfer[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TransferController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'storageCashIn' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/cash-in[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CashInController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'storageMetalExpense' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/metal-expense[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\MetalExpenseController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'storageCashTotal' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/cash-total[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'department' => '[0-9]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CashTotalController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'storage_cash_total_legal' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/storage/cash-total-legal[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CashTotalController::class,
                        'action' => 'legal',
                    ],
                ],
            ],
            'storageCashTransfer' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[dep[:department]/]storage/cash-transfer[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\CashTransferController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'office_cash_in' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/office/cash-in[/clear/:clear][/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'department' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OfficeCashInController::class,
                        'action' => 'index',
                        'department' => 0
                    ],
                ],
            ],
            'api_weighing_save' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api/weighing',
                    'defaults' => [
                        'controller' => Controller\WeighingController::class,
                        'action' => 'save',
                    ]
                ]
            ],
            'weighing' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/reports/weighing[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\WeighingController::class,
                        'action' => 'index',
                    ],
                ]
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\PurchaseController::class => PurchaseControllerFactory::class,
            Controller\PurchaseDealController::class => Controller\Factory\PurchaseDealControllerFactory::class,
            Controller\PurchaseNonFerrousController::class => PurchaseControllerFactory::class,
            Controller\ShipmentController::class => ShipmentControllerFactory::class,
            Controller\ContainerController::class => ContainerControllerFactory::class,
            Controller\ContainerItemController::class => Controller\Factory\ContainerItemControllerFactory::class,
            Controller\TransferController::class => TransferControllerFactory::class,
            Controller\BalanceController::class => BalanceControllerFactory::class,
            Controller\CashInController::class => CashInControllerFactory::class,
            Controller\OfficeCashInController::class => CashInControllerFactory::class,
            Controller\MetalExpenseController::class => MetalExpenseControllerFactory::class,
            Controller\CashTotalController::class => CashTotalControllerFactory::class,
            Controller\CashTransferController::class => ControllerFactory::class,
            Controller\WeighingController::class => Controller\Factory\WeighingControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'storage' => Facade\StorageFactory::class,
            Service\MetalExpenseService::class => Service\Factory\MetalExpenseServiceFactory::class,
            Service\PurchaseService::class => Service\Factory\PurchaseServiceFactory::class,
            Service\CashService::class => Service\Factory\CashServiceFactory::class,
            Service\CashTransferService::class => Service\CashTransferService::class,
            Service\ShipmentService::class => Service\Factory\ShipmentServiceFactory::class,
            Service\ContainerService::class => Service\Factory\ContainerServiceFactory::class,
            Service\ContainerItemService::class => Service\Factory\ContainerItemServiceFactory::class,
            Service\TransferService::class => Service\TransferServiceFactory::class,
            Service\PurchaseDealService::class => Service\PurchaseDealServiceFactory::class,
            //DAO
            Dao\PurchaseDealDao::class => Dao\PurchaseDealDaoFactory::class,
            Dao\CashTotalDao::class => Dao\CashTotalDaoFactory::class,
            Service\WeighingService::class => Service\Factory\WeighingServiceFactory::class,
            Service\WeighingItemService::class => Service\Factory\WeighingItemServiceFactory::class,
            Form\PurchaseForm::class => Form\Factory\PurchaseFormFactory::class,
            Form\PurchaseDealForm::class => Form\Factory\PurchaseDealFormFactory::class,
            Form\ContainerForm::class => Form\Factory\ContainerFormFactory::class,
            Form\ContainerItemForm::class => Form\Factory\ContainerItemFormFactory::class,
            Form\ShipmentForm::class => Form\Factory\ShipmentFormFactory::class,
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
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'Storage\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
