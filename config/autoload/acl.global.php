<?php

$allRoles = [
    'admin',
    'sklad',
    'minisklad',
    'viewer',
    'security',
    'vehicle',
    'colorSklad',
    'glavbuh',
    'officecash',
    'NonFerrousCash',
    'NonFerrousStorage',
    'NonFerrousShipment',
    'NonFerrousTransfer',
    'waybill',
    'shipmentView',
    'warehouse',
    'scalesapi',
    'storekeeper',
    'supply',
    'roma',
    //common roles
    'OfficeCashBankView',
    'SpareView'
];
return [
    'aclmodule' => [
        'allow' => [
            [
                array_merge(['guest'], $allRoles),
                \Api\Controller\AuthController::class,
                ['token']
            ],
            [
                array_merge(['guest'], $allRoles),
                Reference\Controller\UserController::class,
                [
                    'login',
                    'changepass',
                ]
            ],
            [
                [
                    'guest',
                ], Reference\Controller\UserController::class,
                [
                    'login',
                ]
            ],
            [
                $allRoles, Reference\Controller\UserController::class, 'logout'
            ],
            [
                $allRoles,
                Application\Controller\IndexController::class
            ],
            [
                ['viewer'],
                [
                    Storage\Controller\PurchaseController::class,
                    Storage\Controller\PurchaseNonFerrousController::class,
                    Storage\Controller\ShipmentController::class,
                    Storage\Controller\TransferController::class,
                    Storage\Controller\BalanceController::class,
                    Storage\Controller\MetalExpenseController::class,
                    Storage\Controller\CashTotalController::class,
                    Modules\Controller\ScheduledVehicleTripsController::class,
                    Modules\Controller\CompletedVehicleTripsController::class,
                    Modules\Controller\WaybillsController::class,
                    Modules\Controller\ContainerRentalController::class,
                    Spare\Controller\PlanningController::class,
                    Spare\Controller\TotalController::class,
                    Spare\Controller\PaymentController::class,
                    Spare\Controller\BalanceController::class,
                    Spare\Controller\SpareController::class,
                    Spare\Controller\SellerController::class,
                    Spare\Controller\SellerReturnsController::class,
                    Spare\Controller\OrderController::class,
                    Spare\Controller\ReceiptController::class,
                    Spare\Controller\ConsumptionController::class,
                    Spare\Controller\TransferController::class,
                    Spare\Controller\InventoryController::class,
                    ShipmentDocs\Controller\DocumentController::class,
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\OwnerController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                    ShipmentDocs\Controller\RequisitesController::class,
                    ShipmentDocs\Controller\DosimeterController::class,
                    ShipmentDocs\Controller\RepresentativeController::class,
                ],
                ['index']
            ],
            [
                ['viewer'],
                [Reports\Controller\DepExportController::class],
                ['index', 'list'],
            ],
            [
                ['viewer'],
                [
                    Storage\Controller\WeighingController::class,
                ],
                ['index', 'update', 'image-full', 'image-preview']
            ],
            [
                ['admin'],
                [
                    Finance\Controller\TotalController::class,
                    Finance\Controller\MoneyToDepartmentController::class,
                    Finance\Controller\TraderReceiptsController::class,
                    Finance\Controller\OtherReceiptsController::class,
                    Finance\Controller\CashTransferController::class,
                    Finance\Controller\MetalExpenseController::class,
                    Finance\Controller\OtherExpenseController::class,
                    Finance\Controller\TemplateController::class,
                    Reference\Controller\DepartmentController::class,
                    Reference\Controller\EmployeeController::class,
                    Reference\Controller\MetalGroupController::class,
                    Reference\Controller\MetalController::class,
                    Reference\Controller\CustomerController::class,
                    Reference\Controller\TraderController::class,
                    Finance\Controller\BankController::class,
                    Reference\Controller\TraderParentController::class,
                    Reference\Controller\CategoryGroupController::class,
                    Reference\Controller\CategoryController::class,
                    Reference\Controller\ContainerOwnerController::class,
                    Reference\Controller\ShipmentTariffController::class,
                    Reference\Controller\VehicleController::class,
                    Spare\Controller\SpareController::class,
                    Spare\Controller\ReceiptController::class,
                    Spare\Controller\PlanningController::class,
                    Spare\Controller\ConsumptionController::class,
                    Spare\Controller\OrderController::class,
                    Spare\Controller\SellerController::class,
                    Spare\Controller\TotalController::class,
                    Reference\Controller\CustomerDebtController::class,
                    Modules\Controller\WaybillsReportController::class,
                    Modules\Controller\IncomeController::class,
                    Modules\Controller\IncasController::class,
                    Reference\Controller\SettingsController::class,
                    Reference\Controller\WarehouseController::class,
                    Spare\Controller\TransferController::class,
                    Spare\Controller\PaymentController::class,
                    Spare\Controller\InventoryController::class,
                    Spare\Controller\BalanceController::class,
                    Factoring\Controller\SalesController::class,
                    Factoring\Controller\PaymentController::class,
                    Factoring\Controller\TotalController::class,
                    Factoring\Controller\AssignmentDebtController::class,
                    Storage\Controller\ContainerController::class,
                    ShipmentDocs\Controller\DocumentController::class,
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\OwnerController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                    ShipmentDocs\Controller\RequisitesController::class,
                    ShipmentDocs\Controller\DosimeterController::class,
                    ShipmentDocs\Controller\RepresentativeController::class,
                    Reports\Controller\ProfitController::class,
                    Finance\Controller\OfficeOtherExpenseController::class,
                    Finance\Controller\OfficeCashBankTotalController::class,
                    Finance\Controller\OfficeCashTransferController::class,
                    Finance\Controller\OfficeTraderReceiptsController::class,
                    Reports\Controller\ExpensesController::class,
                    Spare\Controller\SellerReturnsController::class,
                    OfficeCash\Controller\TransportIncomeController::class,
                    Storage\Controller\PurchaseDealController::class,
                ],
            ],
            //NonFerrousCash access
            [
                ['NonFerrousCash'],
                [
                    \Reference\Controller\CustomerController::class,
                ],
                ['list', 'list-used'],
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\CashInController ::class,
                    Storage\Controller\CashTotalController::class,
                ],
                ['index', 'add']
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\MetalExpenseController::class,
                ],
                ['index', 'deal']
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\BalanceController::class,
                ],
                ['index']
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\PurchaseController::class,
                ],
                ['index', 'deal', 'add', 'edit', 'save-ajax','save-by-weighing']
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\PurchaseDealController::class,
                ],
                ['check', 'edit']
            ],
            [
                ['NonFerrousCash'],
                [
                    Storage\Controller\WeighingController::class,
                ],
                ['index', 'update']
            ],
            //----------
            //colorSklad access
            [
                ['colorSklad'],
                [
                    Storage\Controller\CashInController ::class,
                ],
                ['index', 'add']
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\CashTotalController::class,
                ],
                ['index','legal', 'legal-data'],
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\MetalExpenseController::class,
                ],
                ['index', 'pay-weighing']
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\BalanceController::class,
                ],
                ['index']
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\PurchaseController::class,
                ],
                ['index', 'save-by-weighing']
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\PurchaseDealController::class,
                ],
                ['check']
            ],
            [
                ['colorSklad'],
                Storage\Controller\TransferController::class,
                ['index','add']
            ],
            [
                ['colorSklad'],
                [
                    Storage\Controller\WeighingController::class,
                ],
                ['index', 'update', 'image-full', 'image-preview']
            ],
            //Access colorSklad to shipmentDocs
            [
                'colorSklad',
                ShipmentDocs\Controller\DocumentController::class,
                [
                    'index',
                    'add',
                    'save',
                    'pdf-packing-list',
                    'pdf-transport-waybill',
                    'pdf-packing-transport',
                    'pdf-letter-of-authority',
                    'pdf-explosive-radiation-certificate',
                ],
            ],
            [
                'colorSklad',
                Storage\Controller\ContainerController::class,
                [
                    'get-color-departments-containers-by-date'
                ],
            ],
            [
                'colorSklad',
                [
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                ],
                ['index', 'add', 'save', 'edit', 'delete'],
            ],
            [
                'colorSklad',
                ShipmentDocs\Controller\OwnerController::class,
                ['index', 'get', 'list', 'save', 'delete'],
            ],
            [
                'colorSklad',
                ShipmentDocs\Controller\RequisitesController::class,
                ['index'],
            ],
            //----------
            //NonFerrousStorage access
            [
                ['NonFerrousStorage'],
                [
                    Storage\Controller\PurchaseController::class,
                ],
                ['index', 'add', 'edit', 'save-ajax',]
            ],
            [
                ['NonFerrousStorage'],
                [
                    Storage\Controller\PurchaseDealController::class,
                ],
                ['check', 'edit']
            ],
            [
                ['NonFerrousStorage'],
                [
                    \Reference\Controller\CustomerController::class,
                ],
                ['list', 'list-used'],
            ],
            [
                ['NonFerrousStorage'],
                [
                    Storage\Controller\BalanceController::class,
                ],
                ['index']
            ],
            //----------
            //NonFerrousShipment access
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\ShipmentController::class,
                ],
                ['index','add', 'add-shipment', 'save-ajax']
            ],
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\ContainerItemController::class,
                ],
                ['add']
            ],
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\ContainerController::class,
                ],
                ['add']
            ],
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\PurchaseController::class,
                ],
                ['index', 'deal']
            ],
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\BalanceController::class,
                ],
                ['index']
            ],
            [
                ['NonFerrousShipment'], Storage\Controller\TransferController::class, ['index','add','edit']
            ],
            [
                ['NonFerrousShipment'],
                [
                    Storage\Controller\WeighingController::class,
                ],
                ['index', 'image-full', 'image-preview']
            ],
            //----------
            //NonFerrousTransfer access
            [
                ['NonFerrousTransfer'], Storage\Controller\TransferController::class, ['index','add']
            ],
            [
                ['NonFerrousTransfer'], [
                    Storage\Controller\BalanceController::class,
                    Storage\Controller\PurchaseController::class
                ], ['index']
            ],
            //----------
            //sklad access
            [
                ['sklad'],
                [
                    Storage\Controller\ShipmentController::class,
                    Storage\Controller\BalanceController::class,
                    Storage\Controller\CashInController::class,
                    Storage\Controller\MetalExpenseController::class,
                ],
                ['index', 'add', 'add-shipment']
            ],
            [
                ['sklad'],
                [
                    Storage\Controller\ContainerItemController::class,
                ],
                ['add']
            ],
            [
                ['sklad'],
                [
                    Storage\Controller\CashTotalController::class,
                ],
                ['index','legal', 'legal-data'],
            ],
            [
                ['sklad'],
                [
                    Storage\Controller\ContainerController::class,
                ],
                ['add']
            ],
            [
                ['sklad'],
                [
                    Modules\Controller\ScheduledVehicleTripsController::class,
                    Modules\Controller\CompletedVehicleTripsController::class,
                ],
                ['index']
            ],
            //--------------
            [
                ['sklad'], Storage\Controller\TransferController::class, ['index','add','edit']
            ],
            [
                ['sklad'], Storage\Controller\PurchaseController::class, ['index','add', 'edit', 'save-ajax',],
            ],
            [
                ['sklad'], \Reference\Controller\CustomerController::class, ['list', 'list-used'],
            ],
            [
                ['sklad'], Storage\Controller\ShipmentController::class, ['index','add', 'save-ajax'],
            ],
            [
                ['minisklad'], Storage\Controller\PurchaseController::class, ['index','add', 'edit', 'save-ajax',],
            ],
            [
                ['minisklad'], \Reference\Controller\CustomerController::class, ['list', 'list-used'],
            ],
            [
                ['minisklad'],
                [
                    Storage\Controller\BalanceController::class,
                    Storage\Controller\CashInController::class,
                    Storage\Controller\MetalExpenseController::class,
                ],
                ['index','add'],
            ],
            [
                ['minisklad'],
                [
                    Storage\Controller\CashTotalController::class,
                ],
                ['index','legal', 'legal-data'],
            ],
            [
                ['minisklad'],
                [
                    Storage\Controller\TransferController::class,
                ],
                ['index','add', 'edit'],
            ],
            //Officecash access
            [
                'officecash',
                Storage\Controller\OfficeCashInController::class,
                ['index','add'],
            ],
            [
                'officecash',
                OfficeCash\Controller\OfficeOtherExpenseController::class,
                ['index', 'add', 'editPartial'],
            ],
            [
                'officecash',
                OfficeCash\Controller\OfficeCashTotalController::class,
                'index',
            ],
            [
                'officecash',
                Finance\Controller\OfficeOtherExpenseController::class,
                ['import', 'edit', 'save'],
            ],
            [
                'officecash',
                Finance\Controller\TemplateController::class,
                ['index', 'add', 'edit', 'delete'],
            ],
            [
                'officecash',
                [
                    Finance\Controller\OfficeOtherReceiptsController::class,
                ],
                ['index', 'json'],
            ],
            [
                ['officecash'],
                [
                    \Finance\Controller\MetalExpenseController::class
                ],
                ['index', 'list', 'customers', 'bank-accounts']
            ],
            [
                'officecash',
                \Reference\Controller\CustomerController::class,
                ['list', 'list-used'],
            ],
            [
                'officecash',
                Storage\Controller\PurchaseController::class,
                ['index']
            ],
            [
                ['officecash'],
                [
                    Storage\Controller\PurchaseDealController::class,
                ],
                ['check']
            ],
            [
                'officecash',
                Storage\Controller\ShipmentController::class,
                ['index', 'add', 'edit', 'money', 'add-shipment', 'save-ajax', 'get-item'],
            ],
            [
                'officecash',
                Storage\Controller\ContainerItemController::class,
                ['edit', 'add'],
            ],
            [
                'officecash',
                Storage\Controller\ContainerController::class,
                ['add'],
            ],
            [
                'officecash',
                Storage\Controller\CashTotalController::class,
                ['index', 'editDateCustomer']
            ],
            [
                'officecash',
                Storage\Controller\MetalExpenseController::class,
                'index'
            ],
            [
                'officecash',
                [
                    Modules\Controller\CompletedVehicleTripsController::class,
                    Modules\Controller\ScheduledVehicleTripsController::class,
                ],
                'index'
            ],
            [
                ['officecash'],
                [
                    Modules\Controller\IncomeController::class,
                ],
                ['index', 'list']
            ],
            [
                'officecash',
                Storage\Controller\TransferController::class,
                'index'
            ],
            [
                ['officecash'],
                [\Spare\Controller\PlanningController::class],
                ['index', 'add', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['officecash'],
                [\Spare\Controller\OrderController::class],
                ['index', 'add', 'getPlanning', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['officecash'],
                [\Spare\Controller\ReceiptController::class],
                ['index', 'add', 'getOrders', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['officecash'],
                [
                    \Spare\Controller\TotalController::class,
                    \Spare\Controller\BalanceController::class
                ],
                ['index']
            ],
            [
                ['officecash'],
                [
                    \Spare\Controller\ConsumptionController::class,
                    \Spare\Controller\TransferController::class
                ],
                ['index', 'add', 'edit', 'save', 'delete', 'update', 'export-to-pdf']
            ],
            [
                ['officecash'],
                [
                    \Spare\Controller\InventoryController::class,
                ],
                ['index', 'add', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['officecash'],
                [
                    \Spare\Controller\PaymentController::class,
                ],
                ['index', 'json', 'saveBind', 'removeBind', 'cash-index', 'cash-json', 'save-cash-bind', 'remove-cash-bind']
            ],
            [
                ['officecash'],
                [
                    \OfficeCash\Controller\TransportIncomeController::class,
                ],
                ['index', 'list', 'add', 'save']
            ],
            [
                'officecash',
                [
                    Factoring\Controller\SalesController::class,
                    Factoring\Controller\PaymentController::class,
                    Factoring\Controller\TotalController::class,
                    Factoring\Controller\AssignmentDebtController::class,
                ],
                ['index'],
            ],
            //--------------------
            //Glavbuh access
            [
                'glavbuh',
                [
                    Finance\Controller\TraderReceiptsController::class,
                    Finance\Controller\OtherReceiptsController::class,
                    Finance\Controller\MetalExpenseController::class,
                    Finance\Controller\CashTransferController::class,
                    Finance\Controller\OfficeOtherExpenseController::class,
                    Finance\Controller\OfficeCashBankTotalController::class,
                    Finance\Controller\OfficeCashTransferController::class,
                    Finance\Controller\OfficeTraderReceiptsController::class,
                    Finance\Controller\OfficeOtherReceiptsController::class,
                    Finance\Controller\MoneyToDepartmentController::class,
                    Modules\Controller\WaybillsController::class,
                    Storage\Controller\PurchaseNonFerrousController::class,
                    Storage\Controller\PurchaseController::class,
                    Storage\Controller\PurchaseDealController::class,
                    Storage\Controller\ShipmentController::class,
                    Storage\Controller\ContainerController::class,
                    Storage\Controller\ContainerItemController::class,
                    Storage\Controller\TransferController::class,
                    Storage\Controller\BalanceController::class,
                    Storage\Controller\MetalExpenseController::class,
                    Storage\Controller\CashTransferController::class,
                    Storage\Controller\CashTotalController::class,
                    Storage\Controller\WeighingController::class,
                    Storage\Controller\CashInController::class,
                    Storage\Controller\OfficeCashInController::class,
                    OfficeCash\Controller\TransportIncomeController::class,
                    Factoring\Controller\SalesController::class,
                    Factoring\Controller\PaymentController::class,
                    Factoring\Controller\TotalController::class,
                    Factoring\Controller\AssignmentDebtController::class,
                    Reports\Controller\DepExportController::class,
                    Spare\Controller\PlanningController::class,
                    Spare\Controller\TotalController::class,
                    Spare\Controller\PaymentController::class,
                    Spare\Controller\BalanceController::class,
                    Spare\Controller\SpareController::class,
                    Spare\Controller\SellerController::class,
                    Spare\Controller\SellerReturnsController::class,
                    Spare\Controller\OrderController::class,
                    Spare\Controller\ReceiptController::class,
                    Spare\Controller\ConsumptionController::class,
                    Spare\Controller\TransferController::class,
                    Spare\Controller\InventoryController::class,
                    Modules\Controller\ScheduledVehicleTripsController::class,
                    Modules\Controller\CompletedVehicleTripsController::class,
                    Modules\Controller\WaybillsController::class,
                    Modules\Controller\WaybillsReportController::class,
                    Modules\Controller\WaybillSettingsController::class,
                    Modules\Controller\ContainerRentalController::class,
                    Modules\Controller\IncomeController::class,
                    Modules\Controller\IncasController::class,
                    Reference\Controller\CustomerController::class,
                    Reference\Controller\MetalController::class,
                    Reference\Controller\CustomerController::class,
                    Reference\Controller\MetalGroupController::class,
                    Reference\Controller\EmployeeController::class,
                    Reference\Controller\TraderController::class,
                    Reference\Controller\TraderParentController::class,
                    Reference\Controller\ShipmentTariffController::class,
                    Reference\Controller\ContainerOwnerController::class,
                    Reference\Controller\VehicleController::class,
                    Reference\Controller\SettingsController::class,
                    ShipmentDocs\Controller\DocumentController::class,
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                    ShipmentDocs\Controller\OwnerController::class,
                    ShipmentDocs\Controller\RequisitesController::class,
                    ShipmentDocs\Controller\DosimeterController::class,
                    ShipmentDocs\Controller\RepresentativeController::class,
                ]
            ],
            [
                ['vehicle'],
                [
                    Modules\Controller\ScheduledVehicleTripsController::class,
                    Modules\Controller\CompletedVehicleTripsController::class,
                ],
                ['index', 'add', 'edit', 'complete']
            ],
            [
                ['vehicle'],
                [
                    Modules\Controller\IncomeController::class,
                    Modules\Controller\IncasController::class,
                ],
                ['index', 'list']
            ],
            [
                ['vehicle'],
                Reports\Controller\DepExportController::class,
                ['index','photo','list','get-numbers']
            ],
            [
                ['vehicle'],
                [\Spare\Controller\TotalController::class],
                ['index']
            ],
            //waybill access бухгалтеры
            [
                ['waybill'],
                [
                    Modules\Controller\WaybillsController::class,
                    Modules\Controller\WaybillsReportController::class
                ],
                ['index', 'create']
            ],
            [
                'waybill',
                ShipmentDocs\Controller\DocumentController::class,
                [
                    'index',
                    'add',
                    'save',
                    'pdf-packing-list',
                    'pdf-transport-waybill',
                    'pdf-packing-transport',
                    'pdf-letter-of-authority',
                    'pdf-explosive-radiation-certificate',
                ],
            ],
            [
                'waybill',
                Storage\Controller\ContainerController::class,
                [
                    'get-color-departments-containers-by-date'
                ],
            ],
            [
                'waybill',
                [
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                ],
                ['index', 'add', 'save', 'edit', 'delete'],
            ],
            [
                'waybill',
                ShipmentDocs\Controller\OwnerController::class,
                ['index', 'get', 'list', 'save', 'delete'],
            ],
            [
                'waybill',
                ShipmentDocs\Controller\RequisitesController::class,
                ['index'],
            ],
            //
            [
                ['shipmentView'],
                [
                    \Storage\Controller\ShipmentController::class
                ],
                ['index']
            ],
            [
                ['shipmentView'],
                [
                    \Factoring\Controller\PaymentController::class
                ],
                ['index', 'confirm']
            ],
            //security
            [
                ['security'],
                Reports\Controller\DepExportController::class,
                ['index','photo','list','get-numbers'] // Только выезды техники
            ],
            [
                ['security'],
                [
                    Spare\Controller\SpareController::class,
                ],
                ['index','add','edit']
            ],
            [
                ['admin'], null // Полный доступ
            ],
            //warehouse access
            [
                ['warehouse'],
                [\Spare\Controller\PlanningController::class],
                ['index', 'add', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['warehouse'],
                [\Spare\Controller\ReceiptController::class],
                ['index', 'add', 'getOrders', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['warehouse'],
                [
                    \Spare\Controller\ConsumptionController::class,
                    \Spare\Controller\TransferController::class
                ],
                ['index', 'add', 'edit', 'save', 'delete', 'update', 'export-to-pdf']
            ],
            [
                ['warehouse'],
                [\Spare\Controller\TotalController::class],
                ['index']
            ],
            [
                ['warehouse'],
                [
                    \Spare\Controller\SpareController::class,
                    \Spare\Controller\SellerController::class
                ],
                ['index', 'add', 'edit']
            ],
            [
                ['warehouse'],
                [
                    \Spare\Controller\InventoryController::class,
                ],
                ['index', 'add', 'edit', 'save', 'delete', 'update']
            ],
            //scalesapi access
            [
                ['scalesapi'],
                [
                    \Reference\Controller\MetalController::class,
                    \Reference\Controller\DepartmentController::class
                ],
                ['list']
            ],
            [
                ['scalesapi'],
                [\Storage\Controller\WeighingController::class],
                ['save']
            ],
            //storekeeper access
            [
                ['storekeeper'],
                [\Spare\Controller\TotalController::class],
                ['index']
            ],
            //supply access
            [
                ['supply'],
                [\Spare\Controller\BalanceController::class],
                ['index']
            ],
            [
                ['supply'],
                [\Spare\Controller\PaymentController::class],
                ['index', 'json', 'saveBind', 'removeBind', 'cash-index', 'cash-json', 'save-cash-bind', 'remove-cash-bind']
            ],
            [
                ['supply'],
                [\Spare\Controller\OrderController::class],
                ['index', 'add', 'getPlanning', 'edit', 'save', 'delete', 'update']
            ],
            [
                ['supply'],
                [\Spare\Controller\PlanningController::class],
                ['index', 'in-work', 'delete-item']
            ],
            [
                ['supply'],
                [\Spare\Controller\ReceiptController::class],
                ['index']
            ],
            [
                ['supply'],
                [\Spare\Controller\SellerController::class],
                ['index', 'add', 'edit']
            ],
            [
                ['supply'],
                [\Reference\Controller\VehicleController::class],
                ['index', 'add', 'edit']
            ],
            //roma access
            [
                ['roma'],
                [\Storage\Controller\CashInController::class],
                ['index', 'add', 'edit']
            ],
            [
                'roma',
                [
                    ShipmentDocs\Controller\DriverController::class,
                    ShipmentDocs\Controller\ReceiverController::class,
                ],
                ['index', 'add', 'save', 'edit', 'delete'],
            ],
            [
                'roma',
                ShipmentDocs\Controller\OwnerController::class,
                ['index', 'get', 'save', 'delete'],
            ],
            [
                'roma',
                ShipmentDocs\Controller\RequisitesController::class,
                ['index'],
            ],
            //common roles
            [
                'OfficeCashBankView',
                [
                    Finance\Controller\OfficeOtherExpenseController::class,
                    Finance\Controller\OfficeCashBankTotalController::class,
                    Finance\Controller\OfficeCashTransferController::class,
                    Finance\Controller\OfficeTraderReceiptsController::class,
                ],
                ['index', 'json'],
            ],
            [
                ['SpareView'],
                [
                    \Spare\Controller\PlanningController::class,
                    \Spare\Controller\OrderController::class,
                    \Spare\Controller\ReceiptController::class,
                    \Spare\Controller\TotalController::class,
                    \Spare\Controller\BalanceController::class,
                    \Spare\Controller\ConsumptionController::class,
                    \Spare\Controller\TransferController::class,
                    \Spare\Controller\SpareController::class,
                    \Spare\Controller\SellerController::class
                ],
                ['index']
            ],
            [
                ['SpareView'],
                [
                    \Spare\Controller\PaymentController::class,
                ],
                ['index', 'json', 'cash-index', 'cash-json']
            ],
        ],
        'deny' => [
            [
            ],
        ],
    ],
];
