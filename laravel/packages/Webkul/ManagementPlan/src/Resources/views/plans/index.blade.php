<x-admin::layouts>
    <x-slot:title>
        {{ trans('management_plan::app.titles.setup') }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h1 class="text-xl font-bold dark:text-white">
                {{ trans('management_plan::app.titles.setup') }}
            </h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Create plans, assign them to users, and define assignment periods.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
                {{ session('error') }}
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

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold dark:text-white">Create plan</h2>

                <form method="POST" action="{{ route('admin.management_plans.plans.store') }}" class="mt-3 flex flex-col gap-3">
                    @csrf

                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="Plan name"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >

                    <textarea
                        name="description"
                        rows="3"
                        placeholder="Description"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    ></textarea>

                    <label class="inline-flex items-center gap-2 text-sm dark:text-gray-200">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active
                    </label>

                    <button
                        type="submit"
                        class="primary-button w-fit"
                    >
                        Create
                    </button>
                </form>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-base font-semibold dark:text-white">Assign plan to user</h2>

                <form method="POST" action="{{ route('admin.management_plans.assignments.store') }}" class="mt-3 flex flex-col gap-3">
                    @csrf

                    <select
                        name="management_plan_id"
                        required
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Select plan</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>

                    <select
                        name="user_id"
                        required
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Select user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->view_permission }})
                            </option>
                        @endforeach
                    </select>

                    <div class="grid gap-3 md:grid-cols-2">
                        <input
                            type="date"
                            name="start_date"
                            required
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >

                        <input
                            type="date"
                            name="end_date"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                    </div>

                    <button
                        type="submit"
                        class="primary-button w-fit"
                    >
                        Assign
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold dark:text-white">Existing plans</h2>

            @if ($plans->isEmpty())
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">No plans created yet.</p>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Description</th>
                                <th class="px-3 py-2">Active</th>
                                <th class="px-3 py-2">Assignments</th>
                                <th class="px-3 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($plans as $plan)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input
                                            form="update-plan-{{ $plan->id }}"
                                            type="text"
                                            name="name"
                                            value="{{ $plan->name }}"
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        >
                                    </td>
                                    <td class="px-3 py-2">
                                        <input
                                            form="update-plan-{{ $plan->id }}"
                                            type="text"
                                            name="description"
                                            value="{{ $plan->description }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        >
                                    </td>
                                    <td class="px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm dark:text-gray-200">
                                            <input
                                                form="update-plan-{{ $plan->id }}"
                                                type="checkbox"
                                                name="is_active"
                                                value="1"
                                                @checked($plan->is_active)
                                            >
                                            {{ $plan->is_active ? 'Yes' : 'No' }}
                                        </label>
                                    </td>
                                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
                                        {{ $plan->assignments_count }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex gap-2">
                                            <form id="update-plan-{{ $plan->id }}" method="POST" action="{{ route('admin.management_plans.plans.update', $plan->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <button
                                                    type="submit"
                                                    class="secondary-button"
                                                >
                                                    Update
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.management_plans.plans.destroy', $plan->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="secondary-button border-red-500 !bg-red-500 hover:!bg-red-600"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-base font-semibold dark:text-white">Assignments</h2>

            @if ($assignments->isEmpty())
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">No assignments created yet.</p>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Plan</th>
                                <th class="px-3 py-2">Start</th>
                                <th class="px-3 py-2">End</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($assignments as $assignment)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $assignment->user?->name }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $assignment->plan?->name }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ $assignment->start_date?->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                                        {{ $assignment->end_date?->format('Y-m-d') ?: '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <form method="POST" action="{{ route('admin.management_plans.assignments.destroy', $assignment->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="secondary-button border-red-500 !bg-red-500 hover:!bg-red-600"
                                            >
                                                Remove
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
