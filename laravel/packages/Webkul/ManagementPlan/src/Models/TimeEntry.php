<?php

namespace Webkul\ManagementPlan\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ManagementPlan\Contracts\TimeEntry as TimeEntryContract;
use Webkul\User\Models\UserProxy;

class TimeEntry extends Model implements TimeEntryContract
{
    protected $table = 'management_plan_time_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'management_plan_id',
        'user_id',
        'entry_month',
        'hours',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'entry_month' => 'date',
        'hours' => 'decimal:2',
    ];

    /**
     * Get the plan for this entry.
     */
    public function plan()
    {
        return $this->belongsTo(ManagementPlanProxy::modelClass(), 'management_plan_id');
    }

    /**
     * Get the user for this entry.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'user_id');
    }
}
