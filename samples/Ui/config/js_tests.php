<?php

return [
    'unit' => [
        'children' => [
            'ui' => ['title' => osm_t("UI"), 'route' => 'GET /tests/unit/ui'],
        ],
    ],
    'ui' => [
        'title' => osm_t("UI Components"),
        'children' => [
            'colors' => ['title' => osm_t("Colors"), 'route' => 'GET /tests/ui/colors'],
            'typography' => ['title' => osm_t("Typography"), 'route' => 'GET /tests/ui/typography'],
            'buttons' => ['title' => osm_t("Buttons"), 'route' => 'GET /tests/ui/buttons'],
            'menus' => ['title' => osm_t("Menus"), 'route' => 'GET /tests/ui/menus'],
            'dialogs' => ['title' => osm_t("Dialogs"), 'route' => 'GET /tests/ui/dialogs'],
            'snack-bars' => ['title' => osm_t("Snack Bars"), 'route' => 'GET /tests/ui/snack-bars'],
            'lists' => ['title' => osm_t("Lists"), 'route' => 'GET /tests/ui/lists'],
            'tables' => ['title' => osm_t("Tables"), 'route' => 'GET /tests/ui/tables'],
            'uploads' => ['title' => osm_t("Uploads"), 'route' => 'GET /tests/ui/uploads'],
        ],
    ],
];