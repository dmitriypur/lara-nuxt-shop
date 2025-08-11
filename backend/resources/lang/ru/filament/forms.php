<?php

return [

    'fields' => [

        'code_editor' => [

            'actions' => [

                'copy_to_clipboard' => [
                    'label' => 'Копировать в буфер обмена',
                ],

            ],

        ],

        'color_picker' => [

            'actions' => [

                'set' => [
                    'label' => 'Установить',
                ],

            ],

        ],

        'file_upload' => [

            'editor' => [

                'actions' => [

                    'cancel' => [
                        'label' => 'Отмена',
                    ],

                    'drag_crop' => [
                        'label' => 'Режим перетаскивания "обрезка"',
                    ],

                    'drag_move' => [
                        'label' => 'Режим перетаскивания "перемещение"',
                    ],

                    'flip_horizontal' => [
                        'label' => 'Отразить изображение по горизонтали',
                    ],

                    'flip_vertical' => [
                        'label' => 'Отразить изображение по вертикали',
                    ],

                    'move_down' => [
                        'label' => 'Переместить изображение вниз',
                    ],

                    'move_left' => [
                        'label' => 'Переместить изображение влево',
                    ],

                    'move_right' => [
                        'label' => 'Переместить изображение вправо',
                    ],

                    'move_up' => [
                        'label' => 'Переместить изображение вверх',
                    ],

                    'reset' => [
                        'label' => 'Сброс',
                    ],

                    'rotate_left' => [
                        'label' => 'Повернуть изображение влево',
                    ],

                    'rotate_right' => [
                        'label' => 'Повернуть изображение вправо',
                    ],

                    'save' => [
                        'label' => 'Сохранить',
                    ],

                    'zoom_100' => [
                        'label' => 'Масштабировать изображение до 100%',
                    ],

                    'zoom_in' => [
                        'label' => 'Увеличить',
                    ],

                    'zoom_out' => [
                        'label' => 'Уменьшить',
                    ],

                ],

                'fields' => [

                    'height' => [
                        'label' => 'Высота',
                        'unit' => 'пикс',
                    ],

                    'rotation' => [
                        'label' => 'Поворот',
                        'unit' => 'град',
                    ],

                    'width' => [
                        'label' => 'Ширина',
                        'unit' => 'пикс',
                    ],

                    'x_position' => [
                        'label' => 'X',
                        'unit' => 'пикс',
                    ],

                    'y_position' => [
                        'label' => 'Y',
                        'unit' => 'пикс',
                    ],

                ],

                'aspect_ratios' => [

                    'label' => 'Соотношения сторон',

                    'no_fixed' => [
                        'label' => 'Свободное',
                    ],

                ],

                'svg' => [

                    'messages' => [

                        'confirmation' => 'Редактирование SVG файлов не рекомендуется, так как это может привести к потере качества при масштабировании.\nВы уверены, что хотите продолжить?',
                        'disabled' => 'Редактирование SVG файлов отключено, так как это может привести к потере качества при масштабировании.',

                    ],

                ],

            ],

        ],

        'key_value' => [

            'actions' => [

                'add' => [
                    'label' => 'Добавить строку',
                ],

                'delete' => [
                    'label' => 'Удалить строку',
                ],

                'reorder' => [
                    'label' => 'Изменить порядок строки',
                ],

            ],

            'fields' => [

                'key' => [
                    'label' => 'Ключ',
                ],

                'value' => [
                    'label' => 'Значение',
                ],

            ],

        ],

        'markdown_editor' => [

            'toolbar_buttons' => [
                'attach_files' => 'Прикрепить файлы',
                'blockquote' => 'Цитата',
                'bold' => 'Жирный',
                'bullet_list' => 'Маркированный список',
                'code_block' => 'Блок кода',
                'heading' => 'Заголовок',
                'italic' => 'Курсив',
                'link' => 'Ссылка',
                'ordered_list' => 'Нумерованный список',
                'redo' => 'Повторить',
                'strike' => 'Зачёркнутый',
                'table' => 'Таблица',
                'undo' => 'Отменить',
            ],

        ],

        'repeater' => [

            'actions' => [

                'add' => [
                    'label' => 'Добавить в :label',
                ],

                'add_between' => [
                    'label' => 'Вставить между',
                ],

                'delete' => [
                    'label' => 'Удалить',
                ],

                'clone' => [
                    'label' => 'Клонировать',
                ],

                'collapse' => [
                    'label' => 'Свернуть',
                ],

                'collapse_all' => [
                    'label' => 'Свернуть все',
                ],

                'expand' => [
                    'label' => 'Развернуть',
                ],

                'expand_all' => [
                    'label' => 'Развернуть все',
                ],

                'move_down' => [
                    'label' => 'Переместить вниз',
                ],

                'move_up' => [
                    'label' => 'Переместить вверх',
                ],

                'reorder' => [
                    'label' => 'Изменить порядок',
                ],

            ],

        ],

        'rich_editor' => [

            'dialogs' => [

                'link' => [

                    'actions' => [
                        'link' => 'Ссылка',
                        'unlink' => 'Убрать ссылку',
                    ],

                    'label' => 'URL',

                    'placeholder' => 'Введите URL',

                ],

            ],

            'toolbar_buttons' => [
                'attach_files' => 'Прикрепить файлы',
                'blockquote' => 'Цитата',
                'bold' => 'Жирный',
                'bullet_list' => 'Маркированный список',
                'code_block' => 'Блок кода',
                'h1' => 'Заголовок',
                'h2' => 'Заголовок',
                'h3' => 'Подзаголовок',
                'italic' => 'Курсив',
                'link' => 'Ссылка',
                'ordered_list' => 'Нумерованный список',
                'redo' => 'Повторить',
                'strike' => 'Зачёркнутый',
                'underline' => 'Подчёркнутый',
                'undo' => 'Отменить',
            ],

        ],

        'select' => [

            'actions' => [

                'create_option' => [

                    'modal' => [

                        'heading' => 'Создать',

                        'actions' => [

                            'create' => [
                                'label' => 'Создать',
                            ],

                            'create_another' => [
                                'label' => 'Создать и создать ещё',
                            ],

                        ],

                    ],

                ],

                'edit_option' => [

                    'modal' => [

                        'heading' => 'Редактировать',

                        'actions' => [

                            'save' => [
                                'label' => 'Сохранить',
                            ],

                        ],

                    ],

                ],

            ],

            'boolean' => [
                'true' => 'Да',
                'false' => 'Нет',
            ],

            'loading_message' => 'Загрузка...',

            'max_items_message' => 'Можно выбрать только :count.',

            'no_search_results_message' => 'Нет результатов, соответствующих вашему поиску.',

            'placeholder' => 'Выберите опцию',

            'searching_message' => 'Поиск...',

            'search_prompt' => 'Начните вводить для поиска...',

        ],

        'tags_input' => [

            'placeholder' => 'Новый тег',

        ],

        'text_input' => [

            'actions' => [

                'hide_password' => [
                    'label' => 'Скрыть пароль',
                ],

                'show_password' => [
                    'label' => 'Показать пароль',
                ],

            ],

        ],

        'toggle_buttons' => [

            'boolean' => [
                'true' => 'Да',
                'false' => 'Нет',
            ],

        ],

        'wizard' => [

            'actions' => [

                'previous_step' => [
                    'label' => 'Назад',
                ],

                'next_step' => [
                    'label' => 'Далее',
                ],

            ],

        ],

    ],

    'actions' => [

        'action' => [

            'label' => 'Действие',

        ],

        'cancel' => [

            'label' => 'Отмена',

        ],

        'create' => [

            'label' => 'Создать',

        ],

        'create_another' => [

            'label' => 'Создать и создать ещё',

        ],

        'delete' => [

            'label' => 'Удалить',

        ],

        'edit' => [

            'label' => 'Редактировать',

        ],

        'open' => [

            'label' => 'Открыть',

        ],

        'save' => [

            'label' => 'Сохранить',

        ],

        'save_and_create_another' => [

            'label' => 'Сохранить и создать ещё',

        ],

        'save_and_edit' => [

            'label' => 'Сохранить и продолжить редактирование',

        ],

        'submit' => [

            'label' => 'Отправить',

        ],

        'view' => [

            'label' => 'Просмотр',

        ],

    ],

];