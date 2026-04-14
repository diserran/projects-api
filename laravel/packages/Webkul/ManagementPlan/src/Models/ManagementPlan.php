<?php

namespace Webkul\ManagementPlan\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ManagementPlan\Contracts\ManagementPlan as ManagementPlanContract;

class ManagementPlan extends Model implements ManagementPlanContract
{
    protected $table = 'management_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get plan assignments.
     */
    public function assignments()
    {
        return $this->hasMany(PlanAssignmentProxy::modelClass(), 'management_plan_id');
    }

    /**
     * Get all time entries for this plan.
     */
    public function timeEntries()
    {
        return $this->hasMany(TimeEntryProxy::modelClass(), 'management_plan_id');
    }
}
