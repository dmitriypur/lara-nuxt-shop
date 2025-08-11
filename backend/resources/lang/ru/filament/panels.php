<?php

return [

    'components' => [

        'breadcrumbs' => [

            'label' => 'Навигация',

        ],

        'logo' => [

            'label' => 'Домой',

        ],

    ],

    'layout' => [

        'actions' => [

            'sidebar' => [

                'collapse' => [
                    'label' => 'Свернуть боковую панель',
                ],

                'expand' => [
                    'label' => 'Развернуть боковую панель',
                ],

            ],

            'theme_switcher' => [

                'dark' => [
                    'label' => 'Включить тёмную тему',
                ],

                'light' => [
                    'label' => 'Включить светлую тему',
                ],

                'system' => [
                    'label' => 'Включить системную тему',
                ],

            ],

            'user_menu' => [

                'account' => [
                    'label' => 'Аккаунт',
                ],

                'logout' => [
                    'label' => 'Выйти',
                ],

            ],

        ],

    ],

    'pages' => [

        'auth' => [

            'login' => [

                'actions' => [

                    'register' => [
                        'before' => 'или',
                        'label' => 'создайте аккаунт',
                    ],

                    'request_password_reset' => [
                        'label' => 'Забыли пароль?',
                    ],

                ],

                'form' => [

                    'email' => [
                        'label' => 'Адрес электронной почты',
                    ],

                    'password' => [
                        'label' => 'Пароль',
                    ],

                    'remember' => [
                        'label' => 'Запомнить меня',
                    ],

                    'actions' => [

                        'authenticate' => [
                            'label' => 'Войти',
                        ],

                    ],

                ],

                'messages' => [

                    'failed' => 'Неверные учётные данные.',

                ],

                'notifications' => [

                    'throttled' => [
                        'title' => 'Слишком много попыток входа',
                        'body' => 'Пожалуйста, повторите попытку через :seconds секунд.',
                    ],

                ],

            ],

            'logout' => [

                'actions' => [

                    'login' => [
                        'before' => 'или',
                        'label' => 'вернуться к входу',
                    ],

                ],

            ],

            'password_reset' => [

                'request' => [

                    'actions' => [

                        'login' => [
                            'label' => 'вернуться к входу',
                        ],

                    ],

                    'form' => [

                        'email' => [
                            'label' => 'Адрес электронной почты',
                        ],

                        'actions' => [

                            'request' => [
                                'label' => 'Отправить письмо',
                            ],

                        ],

                    ],

                    'messages' => [

                        'throttled' => 'Пожалуйста, подождите перед повторной попыткой.',

                    ],

                    'notifications' => [

                        'throttled' => [
                            'title' => 'Слишком много запросов',
                            'body' => 'Пожалуйста, повторите попытку через :seconds секунд.',
                        ],

                        'sent' => [
                            'title' => 'Письмо отправлено',
                            'body' => 'Проверьте свою электронную почту для получения инструкций.',
                        ],

                    ],

                ],

                'reset' => [

                    'form' => [

                        'email' => [
                            'label' => 'Адрес электронной почты',
                        ],

                        'password' => [
                            'label' => 'Пароль',
                            'validation_attribute' => 'пароль',
                        ],

                        'password_confirmation' => [
                            'label' => 'Подтвердите пароль',
                        ],

                        'actions' => [

                            'reset' => [
                                'label' => 'Сбросить пароль',
                            ],

                        ],

                    ],

                    'notifications' => [

                        'throttled' => [
                            'title' => 'Слишком много попыток',
                            'body' => 'Пожалуйста, повторите попытку через :seconds секунд.',
                        ],

                    ],

                ],

            ],

            'register' => [

                'actions' => [

                    'login' => [
                        'before' => 'или',
                        'label' => 'войдите в свой аккаунт',
                    ],

                ],

                'form' => [

                    'email' => [
                        'label' => 'Адрес электронной почты',
                    ],

                    'name' => [
                        'label' => 'Имя',
                    ],

                    'password' => [
                        'label' => 'Пароль',
                        'validation_attribute' => 'пароль',
                    ],

                    'password_confirmation' => [
                        'label' => 'Подтвердите пароль',
                    ],

                    'actions' => [

                        'register' => [
                            'label' => 'Зарегистрироваться',
                        ],

                    ],

                ],

                'notifications' => [

                    'throttled' => [
                        'title' => 'Слишком много попыток регистрации',
                        'body' => 'Пожалуйста, повторите попытку через :seconds секунд.',
                    ],

                ],

            ],

        ],

    ],

    'resources' => [

        'pages' => [

            'create_record' => [

                'title' => 'Создать :label',

            ],

            'edit_record' => [

                'title' => 'Редактировать :label',

            ],

            'list_records' => [

                'title' => ':label',

            ],

        ],

    ],

    'widgets' => [

        'account' => [

            'widget' => [

                'actions' => [

                    'edit' => [
                        'label' => 'Редактировать аккаунт',
                    ],

                    'logout' => [
                        'label' => 'Выйти',
                    ],

                ],

            ],

        ],

        'filament_info' => [

            'actions' => [

                'open_documentation' => [
                    'label' => 'Открыть документацию',
                ],

                'open_github' => [
                    'label' => 'Открыть на GitHub',
                ],

            ],

        ],

    ],

];