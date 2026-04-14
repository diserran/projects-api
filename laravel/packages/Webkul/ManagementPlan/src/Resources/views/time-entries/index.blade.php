<x-admin::layouts>
    <x-slot:title>
        {{ trans('management_plan::app.titles.time-entries') }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold dark:text-white">
                {{ trans('management_plan::app.titles.time-entries') }}
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Register the hours dedicated to each assigned plan for a specific month.
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="GET" action="{{ route('admin.management_plans.time_entries.index') }}" class="flex items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label for="month" class="text-sm font-medium dark:text-white">Month</label>
                    <input
                        id="month"
                        type="month"
                        name="month"
                        value="{{ $month }}"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                >
                    Filter
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            @if ($assignments->isEmpty())
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    No active plan assignments found for this month.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2">Plan</th>
                                <th class="px-3 py-2">Hours</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Notes</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($assignments as $assignment)
                                @php
                                    $entry = $entries->get($assignment->management_plan_id);
                                @endphp

                                <tr>
                                    <td class="px-3 py-3 align-top text-gray-800 dark:text-gray-200">
                                        <div class="font-medium">{{ $assignment->plan?->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Assigned from {{ $assignment->start_date?->format('Y-m-d') }}
                                            @if ($assignment->end_date)
                                                to {{ $assignment->end_date->format('Y-m-d') }}
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                        <form method="POST" action="{{ route('admin.management_plans.time_entries.store') }}" class="flex flex-col gap-2">
                                            @csrf

                                            <input type="hidden" name="management_plan_id" value="{{ $assignment->management_plan_id }}">
                                            <input type="hidden" name="month" value="{{ $month }}">

                                            <input
                                                type="number"
                                                name="hours"
                                                min="0"
                                                max="744"
                                                step="0.25"
                                                required
                                                value="{{ old('hours', $entry?->hours ?? 0) }}"
                                                class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            >
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                            <select
                                                name="status"
                                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            >
                                                <option value="draft" @selected(old('status', $entry?->status) === 'draft')>Draft</option>
                                                <option value="submitted" @selected(old('status', $entry?->status) === 'submitted')>Submitted</option>
                                            </select>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                            <textarea
                                                name="notes"
                                                rows="2"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="Optional notes"
                                            >{{ old('notes', $entry?->notes) }}</textarea>
                                    </td>

                                    <td class="px-3 py-3 align-top">
                                            <button
                                                type="submit"
                                                class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-500"
                                            >
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-admin::layouts>
