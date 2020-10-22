<?php
namespace Spare;

use Application\Controller\ControllerFactory;
use Spare\Controller\Factory\BalanceControllerFactory;
use Spare\Controller\Factory\InventoryControllerFactory;
use Spare\Controller\Factory\OrderControllerFactory;
use Spare\Controller\Factory\ConsumptionControllerFactory;
use Spare\Controller\Factory\PaymentControllerFactory;
use Spare\Controller\Factory\ReceiptControllerFactory;
use Spare\Controller\Factory\PlanningControllerFactory;
use Spare\Controller\Factory\SellerReturnsControllerFactory;
use Spare\Controller\Factory\TransferControllerFactory;
use Spare\Controller\Factory\TotalControllerFactory;
use Spare\Dao\BalanceDaoFactory;
use Spare\Service\Factory\BalanceServiceFactory;
use Spare\Service\Factory\ConsumptionItemServiceFactory;
use Spare\Service\Factory\InventoryServiceFactory;
use Spare\Service\Factory\OrderItemsServiceFactory;
use Spare\Service\Factory\OrderServiceFactory;
use Spare\Service\Factory\ConsumptionServiceFactory;
use Spare\Service\Factory\OrderStatusServiceFactory;
use Spare\Service\Factory\OrderPaymentStatusServiceFactory;
use Spare\Service\Factory\PaymentServiceFactory;
use Spare\Service\Factory\PlanningStatusServiceFactory;
use Spare\Service\Factory\ReceiptServiceFactory;
use Spare\Service\Factory\PlanningItemsServiceFactory;
use Spare\Service\Factory\PlanningServiceFactory;
use Spare\Service\Factory\SellerReturnsServiceFactory;
use Spare\Service\Factory\TotalServiceFactory;
use Spare\Service\Factory\TransferServiceFactory;

return [
    'router' => [
        'routes' => [
            'spare_planning' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/spare/planning[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\PlanningController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'sparePayment' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/spare/payment[/:action][/:id]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\PaymentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareBalance' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/spare/balance[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\BalanceController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'sparesReference' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/spare/reference[/:action][/:id][/clear/:clear]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\SpareController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareSeller' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/reference/seller[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\SellerController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareSellerReturns' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/spare/seller-returns[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\SellerReturnsController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareOrder' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/order[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'warehouse' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\OrderController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareReceipt' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/receipt[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ReceiptController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareConsumption' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/consumption[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ConsumptionController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareTransfer' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/transfer[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TransferController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareInventory' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/inventory[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\InventoryController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'spareTotal' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[warehouse[:warehouse]/]spare/total[/:action][/:id][/clear/:clear]',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\TotalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\PlanningController::class => PlanningControllerFactory::class,
            Controller\ConsumptionController::class => ConsumptionControllerFactory::class,
            Controller\ReceiptController::class => ReceiptControllerFactory::class,
            Controller\OrderController::class => OrderControllerFactory::class,
            Controller\TotalController::class => TotalControllerFactory::class,
            Controller\SellerController::class => ControllerFactory::class,
            Controller\SpareController::class => ControllerFactory::class,
            Controller\TransferController::class => TransferControllerFactory::class,
            Controller\PaymentController::class => PaymentControllerFactory::class,
            Controller\InventoryController::class => InventoryControllerFactory::class,
            Controller\BalanceController::class => BalanceControllerFactory::class,
            Controller\SellerReturnsController::class => SellerReturnsControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'spare' => Facade\SpareFactory::class,
            'sparePlanningService' => PlanningServiceFactory::class,
            'sparePlanningItemsService' => PlanningItemsServiceFactory::class,
            'spareReceiptService' => ReceiptServiceFactory::class,
            'spareConsumptionService' => ConsumptionServiceFactory::class,
            'spareConsumptionItemService' => ConsumptionItemServiceFactory::class,
            'spareOrderService' => OrderServiceFactory::class,
            'spareOrderItemsService' => OrderItemsServiceFactory::class,
            'spareSellerService' => Service\SellerService::class,
            'spareService' => Service\Factory\SpareServiceFactory::class,
            'spareTransferService' => TransferServiceFactory::class,
            'spareTotalService' => TotalServiceFactory::class,
            'spareOrderStatusService' => OrderStatusServiceFactory::class,
            'spareOrderPaymentStatusService' => OrderPaymentStatusServiceFactory::class,
            'sparePlanningStatusService' => PlanningStatusServiceFactory::class,
            'sparePaymentService' => PaymentServiceFactory::class,
            'spareInventoryService' => InventoryServiceFactory::class,
            'spareBalanceService' => BalanceServiceFactory::class,
            'spareBalanceDao' => BalanceDaoFactory::class,
            'sellerReturnsService' => SellerReturnsServiceFactory::class,
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'Application_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'Spare\Entity' => 'Application_driver',
                ],
            ],
        ],
    ],
];
