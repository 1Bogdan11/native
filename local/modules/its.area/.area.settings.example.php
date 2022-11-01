<?php
return [
    'directory' => 'include_area',
    'groups' => [
        'ru' => [
            'name' => 'RU',
            'sort' => 100,
        ],
        'en' => [
            'name' => 'EN',
            'sort' => 200,
        ],
    ],
    'files' => [
        [
            'name' => 'Область первая',
            'sort' => 100,
            'group' => '',
            'path' => 'area_1.php',
            'type' => 'string',
            'default' => '',
        ],
        [
            'name' => 'Область вторая',
            'sort' => 100,
            'group' => 'ru',
            'path' => 'ru/area_2.php',
            'type' => 'text',
            'default' => '',
        ],
        [
            'name' => 'Область третья',
            'sort' => 100,
            'group' => 'ru',
            'path' => 'ru/area_3.php',
            'type' => 'html',
            'default' => '',
        ],
        [
            'name' => 'Область четвертая',
            'sort' => 100,
            'group' => 'ru',
            'path' => 'ru/area_4.php',
            'type' => 'image',
            'default' => '',
        ],
        [
            'name' => 'Область пятая',
            'sort' => 100,
            'group' => 'en',
            'path' => 'en/area_5.php',
            'lines' => [
                'Телефон',
                'Название',
                'Еще поле',
            ],
            'default' => '',
        ],
        [
            'name' => 'Область шестая',
            'sort' => 100,
            'group' => '',
            'path' => 'area_6.php',
            'type' => 'color',
            'default' => '',
        ],
    ],
];
