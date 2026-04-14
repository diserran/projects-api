<?php

namespace Webkul\ManagementPlan\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Webkul\ManagementPlan\Repositories\ManagementPlanRepository;
use Webkul\ManagementPlan\Repositories\PlanAssignmentRepository;
use Webkul\ManagementPlan\Repositories\TimeEntryRepository;
use Webkul\User\Repositories\UserRepository;

class ManagementPlanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ManagementPlanRepository $managementPlanRepository,
        protected PlanAssignmentRepository $planAssignmentRepository,
        protected TimeEntryRepository $timeEntryRepository,
        protected UserRepository $userRepository,
    ) {}

    /**
     * Display monthly user time entries.
     */
    public function timeEntriesIndex(Request $request): View
    {
        $user = auth()->guard('user')->user();

        $month = $this->resolveMonth($request->input('month'));

        $entryMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $assignments = $this->planAssignmentRepository
            ->getModel()
            ->with('plan')
            ->where('user_id', $user->id)
            ->whereDate('start_date', '<=', $entryMonth->toDateString())
            ->where(function ($query) use ($entryMonth) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $entryMonth->toDateString());
            })
            ->orderBy('start_date')
            ->get();

        $entries = $this->timeEntryRepository
            ->getModel()
            ->where('user_id', $user->id)
            ->where('entry_month', $entryMonth->toDateString())
            ->whereIn('management_plan_id', $assignments->pluck('management_plan_id'))
            ->get()
            ->keyBy('management_plan_id');

        return view('management_plan::time-entries.index', compact('assignments', 'entries', 'month'));
    }

    /**
     * Store a monthly entry for the authenticated user.
     */
    public function storeTimeEntry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'management_plan_id' => ['required', 'exists:management_plans,id'],
            'month' => ['required', 'date_format:Y-m'],
            'hours' => ['required', 'numeric', 'min:0', 'max:744'],
            'status' => ['required', 'in:draft,submitted'],
            'notes' => ['nullable', 'string'],
        ]);

        $user = auth()->guard('user')->user();

        $entryMonth = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

        $hasAssignment = $this->planAssignmentRepository
            ->getModel()
            ->where('user_id', $user->id)
            ->where('management_plan_id', $validated['management_plan_id'])
            ->whereDate('start_date', '<=', $entryMonth->toDateString())
            ->where(function ($query) use ($entryMonth) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $entryMonth->toDateString());
            })
            ->exists();

        if (! $hasAssignment) {
            abort(403);
        }

        $this->timeEntryRepository
            ->getModel()
            ->updateOrCreate(
                [
                    'management_plan_id' => $validated['management_plan_id'],
                    'user_id' => $user->id,
                    'entry_month' => $entryMonth->toDateString(),
                ],
                [
                    'hours' => $validated['hours'],
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

        return back()->with('success', trans('management_plan::app.messages.time-entry-saved'));
    }

    /**
     * Display global monthly summary for administrators.
     */
    public function globalIndex(Request $request): View
    {
        $this->ensureGlobalAccess();

        $month = $this->resolveMonth($request->input('month'));

        $entryMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $query = $this->timeEntryRepository
            ->getModel()
            ->with(['user', 'plan'])
            ->where('entry_month', $entryMonth->toDateString());

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('management_plan_id')) {
            $query->where('management_plan_id', $request->integer('management_plan_id'));
        }

        $entries = $query
            ->orderBy('user_id')
            ->orderBy('management_plan_id')
            ->get();

        $groupedEntries = $entries->groupBy(fn ($entry) => $entry->user?->name ?? 'Unknown');

        $totalHours = $entries->sum('hours');

        $users = $this->userRepository
            ->getModel()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $plans = $this->managementPlanRepository
            ->getModel()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('management_plan::global.index', compact(
            'entries',
            'groupedEntries',
            'totalHours',
            'month',
            'users',
            'plans'
        ));
    }

    /**
     * Display plan and assignment setup screen.
     */
    public function plansIndex(): View
    {
        $this->ensureGlobalAccess();

        $plans = $this->managementPlanRepository
            ->getModel()
            ->withCount('assignments')
            ->orderBy('name')
            ->get();

        $users = $this->userRepository
            ->getModel()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'view_permission']);

        $assignments = $this->planAssignmentRepository
            ->getModel()
            ->with(['user', 'plan'])
            ->orderByDesc('start_date')
            ->get();

        return view('management_plan::plans.index', compact('plans', 'users', 'assignments'));
    }

    /**
     * Store a new management plan.
     */
    public function storePlan(Request $request): RedirectResponse
    {
        $this->ensureGlobalAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:management_plans,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->managementPlanRepository->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', trans('management_plan::app.messages.plan-created'));
    }

    /**
     * Update an existing plan.
     */
    public function updatePlan(Request $request, int $id): RedirectResponse
    {
        $this->ensureGlobalAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:management_plans,name,'.$id],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->managementPlanRepository->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ], $id);

        return back()->with('success', trans('management_plan::app.messages.plan-updated'));
    }

    /**
     * Delete a plan.
     */
    public function destroyPlan(int $id): RedirectResponse
    {
        $this->ensureGlobalAccess();

        $this->managementPlanRepository->delete($id);

        return back()->with('success', trans('management_plan::app.messages.plan-deleted'));
    }

    /**
     * Assign a plan to an user.
     */
    public function storeAssignment(Request $request): RedirectResponse
    {
        $this->ensureGlobalAccess();

        $validated = $request->validate([
            'management_plan_id' => ['required', 'exists:management_plans,id'],
            'user_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->toDateString();
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date'])->toDateString() : null;

        $hasOverlap = $this->planAssignmentRepository
            ->getModel()
            ->where('management_plan_id', $validated['management_plan_id'])
            ->where('user_id', $validated['user_id'])
            ->whereDate('start_date', '<=', $endDate ?? '9999-12-31')
            ->where(function ($query) use ($startDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $startDate);
            })
            ->exists();

        if ($hasOverlap) {
            return back()->with('error', trans('management_plan::app.messages.assignment-overlap'));
        }

        $this->planAssignmentRepository->create([
            'management_plan_id' => $validated['management_plan_id'],
            'user_id' => $validated['user_id'],
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return back()->with('success', trans('management_plan::app.messages.assignment-created'));
    }

    /**
     * Delete an assignment.
     */
    public function destroyAssignment(int $id): RedirectResponse
    {
        $this->ensureGlobalAccess();

        $this->planAssignmentRepository->delete($id);

        return back()->with('success', trans('management_plan::app.messages.assignment-deleted'));
    }

    /**
     * Ensure current user can access global setup and reporting.
     */
    protected function ensureGlobalAccess(): void
    {
        $user = auth()->guard('user')->user();

        if ($user->view_permission !== 'global') {
            abort(403);
        }
    }

    /**
     * Resolve the requested month and fallback safely.
     */
    protected function resolveMonth(?string $month): string
    {
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return $month;
        }

        return now()->format('Y-m');
    }
}
