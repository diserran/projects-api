<?php

namespace Webkul\ManagementPlan\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ManagementPlan\Contracts\PlanAssignment as PlanAssignmentContract;
use Webkul\User\Models\UserProxy;

class PlanAssignment extends Model implements PlanAssignmentContract
{
    protected $table = 'management_plan_assignments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'management_plan_id',
        'user_id',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the assigned management plan.
     */
    public function plan()
    {
        return $this->belongsTo(ManagementPlanProxy::modelClass(), 'management_plan_id');
    }

    /**
     * Get the assigned user.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'user_id');
    }
}
