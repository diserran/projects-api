<?php

return [
    [
        'key' => 'management_plans',
        'name' => 'management_plan::app.acl.title',
        'route' => 'admin.management_plans.time_entries.index',
        'sort' => 10,
    ], [
        'key' => 'management_plans.time_entries',
        'name' => 'management_plan::app.acl.time-entries',
        'route' => 'admin.management_plans.time_entries.index',
        'sort' => 1,
    ], [
        'key' => 'management_plans.time_entries.store',
        'name' => 'management_plan::app.acl.time-entries-store',
        'route' => ['admin.management_plans.time_entries.store'],
        'sort' => 1,
    ], [
        'key' => 'management_plans.global',
        'name' => 'management_plan::app.acl.global',
        'route' => 'admin.management_plans.global.index',
        'sort' => 2,
    ], [
        'key' => 'management_plans.setup',
        'name' => 'management_plan::app.acl.setup',
        'route' => ['admin.management_plans.plans.index'],
        'sort' => 3,
    ], [
        'key' => 'management_plans.setup.plans',
        'name' => 'management_plan::app.acl.setup',
        'route' => ['admin.management_plans.plans.index'],
        'sort' => 1,
    ], [
        'key' => 'management_plans.setup.plans.create',
        'name' => 'management_plan::app.acl.create',
        'route' => ['admin.management_plans.plans.store'],
        'sort' => 1,
    ], [
        'key' => 'management_plans.setup.plans.edit',
        'name' => 'management_plan::app.acl.edit',
        'route' => ['admin.management_plans.plans.update'],
        'sort' => 2,
    ], [
        'key' => 'management_plans.setup.plans.delete',
        'name' => 'management_plan::app.acl.delete',
        'route' => ['admin.management_plans.plans.destroy'],
        'sort' => 3,
    ], [
        'key' => 'management_plans.setup.assignments',
        'name' => 'management_plan::app.acl.setup',
        'route' => ['admin.management_plans.assignments.store'],
        'sort' => 2,
    ], [
        'key' => 'management_plans.setup.assignments.create',
        'name' => 'management_plan::app.acl.assignments-create',
        'route' => ['admin.management_plans.assignments.store'],
        'sort' => 1,
    ], [
        'key' => 'management_plans.setup.assignments.delete',
        'name' => 'management_plan::app.acl.assignments-delete',
        'route' => ['admin.management_plans.assignments.destroy'],
        'sort' => 2,
    ],
];
