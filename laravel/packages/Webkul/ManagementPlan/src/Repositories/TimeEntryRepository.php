<?php

namespace Webkul\ManagementPlan\Repositories;

use Webkul\Core\Eloquent\Repository;

class TimeEntryRepository extends Repository
{
    /**
     * Searchable fields.
     *
     * @var array
     */
    protected $fieldSearchable = [
        'management_plan_id',
        'user_id',
        'entry_month',
        'status',
    ];

    /**
     * Specify model class name.
     */
    public function model()
    {
        return 'Webkul\ManagementPlan\Contracts\TimeEntry';
    }
}
