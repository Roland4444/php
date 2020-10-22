<?php

return [
    'menu_top' => [
        'home' => [
            'title' => 'Главная',
            'route' => 'home',
        ],
    ],
    'menu_down' => [
        'chpass' => [
            'title' => 'Сменить пароль',
            'route' => 'user/changepass',
        ],
        'logout' => [
            'title' => ' Выход',
            'route' => 'user/logout',
        ],
    ],
    'menu' => [
        'home' => [
            'title' => 'Главная',
            'route' => 'home',
        ],
        'cash' => [
            'title' => 'Касса администратора',
            'sub-menu' => [
                [
                    'title' => 'Приход от трейдеров',
                    'route' => 'traderReceipts',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Прочие поступления',
                    'route' => 'otherReceipts',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Управленческие расходы',
                    'route' => 'mainOtherExpense',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Расходы в кассу',
                    'route' => 'moneyToDepartment',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Расходы за металл',
                    'route' => 'main_metal_expenses',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Со счета на счет',
                    'route' => 'cashTransfer',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Остатки',
                    'route' => 'mainTotal',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
            ],
        ],
        'storage' => [
            'title' => 'Остатки на складе',
            'route' => 'balance',
            'route-params' => [
                'action' => 'total',
                'clear' => 'yes',
            ],
        ],
        'legalCash' => [
            'title' => 'Сверка с контрагентами',
            'route' => 'storageCashTotal',
            'route-params' => [
                'clear' => 'yes',
            ],
        ],
        'ferrous' => [
            'title' => 'Чёрный металл',
            'sub-menu' => [],
        ],

        'non-ferrous' => [
            'title' => 'Цветной металл',
            'sub-menu' => [
            ]
        ],
        'officecash' => [
            'title' => 'Оператор касса',
            'sub-menu' => [
                [
                    'title' => 'Приход наличных',
                    'route' => 'office_cash_in',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Наличные расходы',
                    'route' => 'office_other_expense',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Деньги от выездов техники',
                    'route' => 'office_transport_income',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Остатки',
                    'route' => 'office_cash_total',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Банки',
                    'sub-menu' => [
                        [
                            'title' => 'Поступления от контрагентов',
                            'route' => 'office_trader_receipts',
                            'route-params' => [
                            ],
                        ],
                        [
                            'title' => 'Прочие поступления',
                            'route' => 'office_other_receipts',
                            'route-params' => [
                            ],
                        ],
                        [
                            'title' => 'Платежи',
                            'route' => 'office_bank_expense',
                            'route-params' => [
                            ],
                        ],
                        [
                            'title' => 'Переводы между счетами',
                            'route' => 'office_cash_transfer',
                            'route-params' => [
                            ],
                        ],
                        [
                            'title' => 'Остатки на счетах',
                            'route' => 'office_bank_total',
                            'route-params' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'factoring' => [
            'title' => 'Факторинг',
            'sub-menu' => [
                [
                    'title' => 'Реализация',
                    'route' => 'factoring_sales',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Оплата',
                    'route' => 'factoring_payments',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Сверка',
                    'route' => 'factoring_total',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Переуступка долга',
                    'route' => 'factoring_assignment_debt',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ]
            ]
        ],
        'reports' => [
            'title' => 'Отчёты',
            'sub-menu' => [
                [
                    'title' => 'Расходы',
                    'route' => 'reportExpenses',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Лимиты',
                    'route' => 'reportExpensesLimits',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Общая прибыль',
                    'route' => 'reportProfit',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Экспорт с площадки',
                    'route' => 'reportDepExport',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                'weighing' => [
                    'title' => 'Экспорт с цветных ПЗУ',
                    'route' => 'weighing',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ]
            ]
        ],
        'warehouse' => [
            'title' => 'Хоз. склад',
            'sub-menu' => [
                [
                    'title' => 'Заявки',
                    'route' => 'spare_planning',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Заказы',
                    'route' => 'spareOrder',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Оплата',
                    'sub-menu' => [
                        [
                            'title' => 'Безналичная',
                            'route' => 'sparePayment',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                        [
                            'title' => 'Наличная',
                            'route' => 'sparePayment',
                            'route-params' => [
                                'action' => 'cash-index',
                                'clear' => 'yes',
                            ],
                        ],
                    ]
                ],
                [
                    'title' => 'Расчеты с поставщиками',
                    'route' => 'spareBalance',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Возврат денег от поставщиков',
                    'route' => 'spareSellerReturns',
                ],
                [
                    'title' => 'Справочники',
                    'sub-menu' => [
                        [
                            'title' => 'Запчасти',
                            'route' => 'sparesReference',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                        [
                            'title' => 'Поставщики',
                            'route' => 'spareSeller',
                            'route-params' => [
                            ],
                        ],
                    ]
                ],
            ]
        ],
        'modules' => [
            'title' => 'Модули',
            'sub-menu' => [
                [
                    'title' => 'Авансы контрагентам',
                    'route' => 'customer_debt',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Выезды техники',
                    'sub-menu' => [
                        [
                            'title' => 'Запланированные',
                            'route' => 'scheduledVehicleTrips',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                        [
                            'title' => 'Завершённые',
                            'route' => 'completedVehicleTrips',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                        [
                            'title' => 'Касса',
                            'route' => 'transportIncome',
                            'route-params' => [
                            ],
                        ],
                        [
                            'title' => 'Инкассация',
                            'route' => 'transportIncas',
                            'route-params' => [
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Пользование',
                    'route' => 'containerRental',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Путевые листы',
                    'sub-menu' => [
                        [
                            'title' => 'Все',
                            'route' => 'waybills',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                        [
                            'title' => 'Отчеты',
                            'route' => 'waybillsReport',
                            'route-params' => [
                                'clear' => 'yes',
                            ],
                        ],
                    ]

                ],
            ]
        ],
        'dict' => [
            'title' => 'Справочники',
            'sub-menu' => [
                [
                    'title' => 'Группы категорий',
                    'route' => 'categoryGroup',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Группы металлов',
                    'route' => 'metalGroup',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Группы трейдеров',
                    'route' => 'reference\traderParent',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Категории расходов',
                    'route' => 'category',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Металл',
                    'route' => 'metal',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Настройки',
                    'route' => 'settings',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Подразделения',
                    'route' => 'department',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Пользователи',
                    'route' => 'user',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Поставщики',
                    'route' => 'customer',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Собственники',
                    'route' => 'containerOwner',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Сотрудники',
                    'route' => 'employee',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Счета',
                    'route' => 'bank',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Тарифы отгрузки',
                    'route' => 'shipmentTariff',
                    'route-params' => [
                    ],
                ],
                [
                    'title' => 'Техника',
                    'route' => 'tech',
                    'route-params' => [
                        'clear' => 'yes'
                    ],
                ],
                [
                    'title' => 'Трейдеры',
                    'route' => 'trader',
                    'route-params' => [
                        'clear' => 'yes',
                    ],
                ],
                [
                    'title' => 'Хоз. склады',
                    'route' => 'warehouse',
                    'route-params' => [
                    ],
                ],
            ]
        ],
        'shipmentDocMenu' => [
            'title' => 'Документы отгрузки',
            'sub-menu' => [
                'documents' => [
                    'title' => 'Документы',
                    'route' => 'shipmentDocs/document',
                    'route-params' => [
                        'clear' => 'yes',
                    ]
                ],
                'references' => [
                    'title' => 'Справочники',
                    'sub-menu' => [
                        'requisites' => [
                            'title' => 'Реквизиты',
                            'route' => 'shipmentDocs/requisites',
                        ],
                        'drivers' => [
                            'title' => 'Водители',
                            'route' => 'shipmentDocs/driver',
                            'route-params' => [
                                'clear' => 'yes',
                            ]
                        ],
                        'owners' => [
                            'title' => 'Перевозчики',
                            'route' => 'shipmentDocs/owner',
                            'route-params' => [
                                'clear' => 'yes',
                            ]
                        ],
                        'receivers' => [
                            'title' => 'Покупатели',
                            'route' => 'shipmentDocs/receiver',
                            'route-params' => [
                                'clear' => 'yes',
                            ]
                        ],
                        'dosimeter' => [
                            'title' => 'Дозиметр',
                            'route' => 'shipmentDocs/dosimeter',
                            'route-params' => [
                            ]
                        ],
                        'representative' => [
                            'title' => 'Представитель',
                            'route' => 'shipmentDocs/representative',
                            'route-params' => [
                            ]
                        ],
                    ],
                ],
            ]
        ]
    ],
    'ferrousMenu' => [
        'all' => [
            [
                'title' => 'Общий приход',
                'route' => 'purchase',
                'route-params' => [
                    'action' => 'total',
                    'clear' => 'yes',
                ],
            ],
        ],
        'departmentMenu' => [
            'store' => [
                'title' => 'Склад',
                'sub-menu' => [
                    [
                        'title' => 'Приход',
                        'route' => 'purchase',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Отгрузка',
                        'route' => 'shipment',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Переброска',
                        'route' => 'transfer',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки',
                        'route' => 'balance',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                ],
            ],
            'cash' => [
                'title' => 'Касса',
                'sub-menu' => [
                    [
                        'title' => 'Приход',
                        'route' => 'storageCashIn',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Расходы за металл',
                        'route' => 'storageMetalExpense',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки',
                        'route' => 'storageCashTotal',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки юр. лица',
                        'route' => 'storage_cash_total_legal',
                        'route-params' => [
                        ],
                    ],
                ],
            ],
        ],
    ],
    'nonFerrousMenu' => [
        'all' => [
            [
                'title' => 'Общий приход',
                'route' => 'purchaseNonFerrous',
                'route-params' => [
                    'action' => 'total',
                    'clear' => 'yes',
                ],
            ]
        ],
        'departmentMenu' => [
            'store' => [
                'title' => 'Склад',
                'sub-menu' => [
                    [
                        'title' => 'Приход',
                        'route' => 'purchase',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Отгрузка',
                        'route' => 'shipment',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Переброска',
                        'route' => 'transfer',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки',
                        'route' => 'balance',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                ],
            ],
            'cash' => [
                'title' => 'Касса',
                'sub-menu' => [
                    [
                        'title' => 'Приход',
                        'route' => 'storageCashIn',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Расход в кассу',
                        'route' => 'storageCashTransfer',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Расходы за металл',
                        'route' => 'storageMetalExpense',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки',
                        'route' => 'storageCashTotal',
                        'route-params' => [
                            'clear' => 'yes',
                        ],
                    ],
                    [
                        'title' => 'Остатки юр. лица',
                        'route' => 'storage_cash_total_legal',
                        'route-params' => [
                        ],
                    ],
                ],
            ]
        ],
    ],
    'warehouseMenu' => [
        'title' => 'Хоз. склад',
        'sub-menu' => [
            [
                'title' => 'Поступление',
                'route' => 'spareReceipt',
                'route-params' => [
                    'clear' => 'yes',
                ],
            ],
            [
                'title' => 'Списание',
                'route' => 'spareConsumption',
                'route-params' => [
                    'clear' => 'yes',
                ],
            ],
            [
                'title' => 'Перемещение',
                'route' => 'spareTransfer',
                'route-params' => [
                    'clear' => 'yes',
                ],
            ],
            [
                'title' => 'Остатки',
                'route' => 'spareTotal',
                'route-params' => [
                    'clear' => 'yes',
                ],
            ],
            [
                'title' => 'Инвентаризация',
                'route' => 'spareInventory',
                'route-params' => [
                    'clear' => 'yes',
                ],
            ],
        ],
    ],

];
