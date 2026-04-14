<?php

namespace Webkul\ManagementPlan\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\ManagementPlan\Models\ManagementPlan;
use Webkul\ManagementPlan\Models\PlanAssignment;
use Webkul\ManagementPlan\Models\TimeEntry;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * The models to be used by this module.
     *
     * @var array
     */
    protected $models = [
        ManagementPlan::class,
        PlanAssignment::class,
        TimeEntry::class,
    ];
}
