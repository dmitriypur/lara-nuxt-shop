<?php

return [

    'attach' => [
        'single' => [
            'label' => 'Прикрепить',
            'modal' => [
                'heading' => 'Прикрепить :label',
                'fields' => [
                    'record_id' => [
                        'label' => 'Запись',
                    ],
                ],
                'actions' => [
                    'attach' => [
                        'label' => 'Прикрепить',
                    ],
                    'attach_another' => [
                        'label' => 'Прикрепить и прикрепить ещё',
                    ],
                ],
            ],
            'notifications' => [
                'attached' => [
                    'title' => 'Прикреплено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Прикрепить выбранные',
            'modal' => [
                'heading' => 'Прикрепить выбранные :label',
                'actions' => [
                    'attach' => [
                        'label' => 'Прикрепить',
                    ],
                ],
            ],
            'notifications' => [
                'attached' => [
                    'title' => 'Прикреплено',
                ],
            ],
        ],
    ],

    'associate' => [
        'single' => [
            'label' => 'Связать',
            'modal' => [
                'heading' => 'Связать :label',
                'fields' => [
                    'record_id' => [
                        'label' => 'Запись',
                    ],
                ],
                'actions' => [
                    'associate' => [
                        'label' => 'Связать',
                    ],
                    'associate_another' => [
                        'label' => 'Связать и связать ещё',
                    ],
                ],
            ],
            'notifications' => [
                'associated' => [
                    'title' => 'Связано',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Связать выбранные',
            'modal' => [
                'heading' => 'Связать выбранные :label',
                'actions' => [
                    'associate' => [
                        'label' => 'Связать',
                    ],
                ],
            ],
            'notifications' => [
                'associated' => [
                    'title' => 'Связано',
                ],
            ],
        ],
    ],

    'clone' => [
        'single' => [
            'label' => 'Клонировать',
            'modal' => [
                'heading' => 'Клонировать :label',
                'actions' => [
                    'clone' => [
                        'label' => 'Клонировать',
                    ],
                    'clone_another' => [
                        'label' => 'Клонировать и клонировать ещё',
                    ],
                ],
            ],
            'notifications' => [
                'cloned' => [
                    'title' => 'Клонировано',
                ],
            ],
        ],
    ],

    'create' => [
        'single' => [
            'label' => 'Создать',
            'modal' => [
                'heading' => 'Создать :label',
                'actions' => [
                    'create' => [
                        'label' => 'Создать',
                    ],
                    'create_another' => [
                        'label' => 'Создать и создать ещё',
                    ],
                ],
            ],
            'notifications' => [
                'created' => [
                    'title' => 'Создано',
                ],
            ],
        ],
    ],

    'delete' => [
        'single' => [
            'label' => 'Удалить',
            'modal' => [
                'heading' => 'Удалить :label',
                'actions' => [
                    'delete' => [
                        'label' => 'Удалить',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Удалено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Удалить выбранные',
            'modal' => [
                'heading' => 'Удалить выбранные :label',
                'actions' => [
                    'delete' => [
                        'label' => 'Удалить',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Удалено',
                ],
            ],
        ],
    ],

    'detach' => [
        'single' => [
            'label' => 'Открепить',
            'modal' => [
                'heading' => 'Открепить :label',
                'actions' => [
                    'detach' => [
                        'label' => 'Открепить',
                    ],
                ],
            ],
            'notifications' => [
                'detached' => [
                    'title' => 'Откреплено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Открепить выбранные',
            'modal' => [
                'heading' => 'Открепить выбранные :label',
                'actions' => [
                    'detach' => [
                        'label' => 'Открепить',
                    ],
                ],
            ],
            'notifications' => [
                'detached' => [
                    'title' => 'Откреплено',
                ],
            ],
        ],
    ],

    'dissociate' => [
        'single' => [
            'label' => 'Разъединить',
            'modal' => [
                'heading' => 'Разъединить :label',
                'actions' => [
                    'dissociate' => [
                        'label' => 'Разъединить',
                    ],
                ],
            ],
            'notifications' => [
                'dissociated' => [
                    'title' => 'Разъединено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Разъединить выбранные',
            'modal' => [
                'heading' => 'Разъединить выбранные :label',
                'actions' => [
                    'dissociate' => [
                        'label' => 'Разъединить',
                    ],
                ],
            ],
            'notifications' => [
                'dissociated' => [
                    'title' => 'Разъединено',
                ],
            ],
        ],
    ],

    'edit' => [
        'single' => [
            'label' => 'Редактировать',
            'modal' => [
                'heading' => 'Редактировать :label',
                'actions' => [
                    'save' => [
                        'label' => 'Сохранить изменения',
                    ],
                ],
            ],
            'notifications' => [
                'saved' => [
                    'title' => 'Сохранено',
                ],
            ],
        ],
    ],

    'export' => [
        'single' => [
            'label' => 'Экспорт',
            'modal' => [
                'heading' => 'Экспорт :label',
                'form' => [
                    'type' => [
                        'label' => 'Тип экспорта',
                        'options' => [
                            'csv' => 'CSV',
                            'xlsx' => 'XLSX',
                        ],
                    ],
                ],
                'actions' => [
                    'export' => [
                        'label' => 'Экспорт',
                    ],
                ],
            ],
            'notifications' => [
                'completed' => [
                    'title' => 'Экспорт завершён',
                    'body' => 'Ваш экспорт готов для скачивания.',
                    'actions' => [
                        'download' => [
                            'label' => 'Скачать',
                        ],
                    ],
                ],
                'max_rows' => [
                    'title' => 'Экспорт слишком большой',
                    'body' => 'Вы не можете экспортировать более :count строк за раз.',
                ],
                'started' => [
                    'title' => 'Экспорт начат',
                    'body' => 'Ваш экспорт начался и вы получите уведомление, когда он будет готов для скачивания.',
                ],
            ],
        ],
    ],

    'force_delete' => [
        'single' => [
            'label' => 'Удалить навсегда',
            'modal' => [
                'heading' => 'Удалить навсегда :label',
                'actions' => [
                    'delete' => [
                        'label' => 'Удалить',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Удалено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Удалить выбранные навсегда',
            'modal' => [
                'heading' => 'Удалить выбранные навсегда :label',
                'actions' => [
                    'delete' => [
                        'label' => 'Удалить',
                    ],
                ],
            ],
            'notifications' => [
                'deleted' => [
                    'title' => 'Удалено',
                ],
            ],
        ],
    ],

    'import' => [
        'single' => [
            'label' => 'Импорт',
            'modal' => [
                'heading' => 'Импорт :label',
                'form' => [
                    'file' => [
                        'label' => 'Файл',
                        'placeholder' => 'Загрузите CSV файл',
                    ],
                    'columns' => [
                        'label' => 'Столбцы',
                        'placeholder' => 'Выберите столбец',
                    ],
                ],
                'actions' => [
                    'download_example' => [
                        'label' => 'Скачать пример CSV файла',
                    ],
                    'import' => [
                        'label' => 'Импорт',
                    ],
                ],
            ],
            'notifications' => [
                'completed' => [
                    'title' => 'Импорт завершён',
                    'actions' => [
                        'download_failed_rows_csv' => [
                            'label' => 'Скачать информацию о неудачных строках|Скачать информацию о неудачных строках',
                        ],
                    ],
                ],
                'max_rows' => [
                    'title' => 'Загруженный CSV файл слишком большой',
                    'body' => 'Вы не можете импортировать более :count строк за раз.',
                ],
                'started' => [
                    'title' => 'Импорт начат',
                    'body' => 'Ваш импорт начался и вы получите уведомление, когда он будет завершён.',
                ],
            ],
            'example_csv' => [
                'file_name' => ':importer_label-пример',
            ],
        ],
    ],

    'replicate' => [
        'single' => [
            'label' => 'Дублировать',
            'modal' => [
                'heading' => 'Дублировать :label',
                'actions' => [
                    'replicate' => [
                        'label' => 'Дублировать',
                    ],
                    'replicate_another' => [
                        'label' => 'Дублировать и дублировать ещё',
                    ],
                ],
            ],
            'notifications' => [
                'replicated' => [
                    'title' => 'Дублировано',
                ],
            ],
        ],
    ],

    'restore' => [
        'single' => [
            'label' => 'Восстановить',
            'modal' => [
                'heading' => 'Восстановить :label',
                'actions' => [
                    'restore' => [
                        'label' => 'Восстановить',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Восстановлено',
                ],
            ],
        ],
        'multiple' => [
            'label' => 'Восстановить выбранные',
            'modal' => [
                'heading' => 'Восстановить выбранные :label',
                'actions' => [
                    'restore' => [
                        'label' => 'Восстановить',
                    ],
                ],
            ],
            'notifications' => [
                'restored' => [
                    'title' => 'Восстановлено',
                ],
            ],
        ],
    ],

    'view' => [
        'single' => [
            'label' => 'Просмотр',
            'modal' => [
                'heading' => 'Просмотр :label',
            ],
        ],
    ],

];