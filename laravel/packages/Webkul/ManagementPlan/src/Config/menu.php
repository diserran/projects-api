<?php

return [
    [
        'key' => 'management_plans',
        'name' => 'management_plan::app.menu.title',
        'route' => 'admin.management_plans.time_entries.index',
        'sort' => 10,
        'icon-class' => 'icon-calendar',
    ], [
        'key' => 'management_plans.time_entries',
        'name' => 'management_plan::app.menu.time-entries',
        'route' => 'admin.management_plans.time_entries.index',
        'sort' => 1,
        'icon-class' => '',
    ], [
        'key' => 'management_plans.global',
        'name' => 'management_plan::app.menu.global',
        'route' => 'admin.management_plans.global.index',
        'sort' => 2,
        'icon-class' => '',
    ], [
        'key' => 'management_plans.setup',
        'name' => 'management_plan::app.menu.setup',
        'route' => 'admin.management_plans.plans.index',
        'sort' => 3,
        'icon-class' => '',
    ],
];
