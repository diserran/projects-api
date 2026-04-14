<x-admin::layouts>
    <x-slot:title>
        {{ trans('management_plan::app.titles.global') }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold dark:text-white">
                {{ trans('management_plan::app.titles.global') }}
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Consolidated monthly report for all users and plans.
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <form method="GET" action="{{ route('admin.management_plans.global.index') }}" class="grid gap-3 md:grid-cols-4">
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

                <div class="flex flex-col gap-1">
                    <label for="user_id" class="text-sm font-medium dark:text-white">User</label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">All users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((int) request('user_id') === $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="management_plan_id" class="text-sm font-medium dark:text-white">Plan</label>
                    <select
                        id="management_plan_id"
                        name="management_plan_id"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">All plans</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected((int) request('management_plan_id') === $plan->id)>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200"
                    >
                        Apply filters
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300">
            <strong>Total hours:</strong> {{ number_format((float) $totalHours, 2) }}
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            @if ($entries->isEmpty())
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    No entries found for the selected criteria.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Plan</th>
                                <th class="px-3 py-2">Hours</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($entries as $entry)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $entry->user?->name }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $entry->plan?->name }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ number_format((float) $entry->hours, 2) }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ ucfirst($entry->status) }}</td>
                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $entry->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($groupedEntries->isNotEmpty())
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold dark:text-white">Hours by user</h2>

                <div class="mt-3 grid gap-2 md:grid-cols-2">
                    @foreach ($groupedEntries as $userName => $items)
                        <div class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700">
                            <span class="font-medium dark:text-white">{{ $userName }}</span>
                            <span class="text-gray-600 dark:text-gray-300">
                                - {{ number_format((float) $items->sum('hours'), 2) }} hours
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-admin::layouts>
