<?php

namespace Webkul\ManagementPlan\Repositories;

use Webkul\Core\Eloquent\Repository;

class ManagementPlanRepository extends Repository
{
    /**
     * Searchable fields.
     *
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'is_active',
    ];

    /**
     * Specify model class name.
     */
    public function model()
    {
        return 'Webkul\ManagementPlan\Contracts\ManagementPlan';
    }
}
