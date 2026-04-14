<?php

namespace Webkul\ManagementPlan\Repositories;

use Webkul\Core\Eloquent\Repository;

class PlanAssignmentRepository extends Repository
{
    /**
     * Searchable fields.
     *
     * @var array
     */
    protected $fieldSearchable = [
        'management_plan_id',
        'user_id',
        'start_date',
        'end_date',
    ];

    /**
     * Specify model class name.
     */
    public function model()
    {
        return 'Webkul\ManagementPlan\Contracts\PlanAssignment';
    }
}
