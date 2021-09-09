<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ $project->name }} ({{ $project->key }})
        </h2>
    </x-slot>

    <x-slot name="sidemenu">
        <x-side-menu-link :href="route('tasks.index', ['project' => $project->id])" :active="request()->routeIs('tasks.index')">
            {{ __('Tasks') }}
        </x-side-menu-link>
        <x-side-menu-link :href="route('tasks.create', ['project' => $project->id])" :active="request()->routeIs('tasks.create')">
            {{ __('Task Create') }}
        </x-side-menu-link>
    </x-slot>

    <div>
        <div class="mx-auto">
            <div class="overflow-hidden sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Tasks') }}
                    </h3>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('tasks.index', ['project' => $project->id]) }}">
            <!-- Validation Errors -->
            <x-flash-message />
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="flex max-w-full mx-auto px-4 py-6 sm:px-6 lg:px-6">
                <div class="md:w-1/4 px-3 mb-6">
                    <x-label for="assigner_id" :value="__('Assigner')" class="{{ $errors->has('assigner_id') ? 'text-red-600' :'' }}" />
                    <x-select :options="$assigners" id="assigner_id" class="block mt-1 w-full {{ $errors->has('assigner_id') ? 'border-red-600' :'' }}" type="text" name="assigner_id" :value="$assigner_id" autofocus />
                </div>

                <div class="md:w-1/3 px-3 mb-6 mr-6">
                    <x-label for="key" :value="__('Keyword')" class="{{ $errors->has('keyword') ? 'text-red-600' :'' }}" />
                    <x-input id="keyword" class="block mt-1 w-full {{ $errors->has('keyword') ? 'border-red-600' :'' }}" type="text" name="keyword" :value="$keyword" :placeholder="__('Keyword')" autofocus />
                </div>
                <div class="flex flex-wrap content-center">
                    <x-button class="px-10">
                        {{ __('Search') }}
                    </x-button>
                </div>
            </div>

            <div class="flex flex-col mx-6 mb-6 bg-white rounded">
                @if(0 < $tasks->count())
                    <div class="flex justify-start p-2">
                        {{ $tasks->appends(request()->query())->links() }}
                    </div>
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('task_kind.name', __('Task Kind'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('id', __('Task Key'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('name', __('Task Name'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('assigner.name', __('Task Assigner'))
                                </th>
                                <th class="py-3 px-6 text-center">
                                    @sortablelink('created_at', __('Created At'))
                                </th>
                                <th class="py-3 px-6 text-center">
                                    @sortablelink('due_date', __('Due Date'))
                                </th>
                                <th class="py-3 px-6 text-center">
                                    @sortablelink('updated_at', __('Updated At'))
                                </th>
                                <th class="py-3 px-6 text-center">
                                    @sortablelink('user.name', __('Created User'))
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($tasks as $task)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer @if($loop->even)bg-gray-50 @endif" onclick="location.href='{{ route('tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}'">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span>{{ $task->task_kind->name }}</span>
                                </td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}">{{ $task->key }}</a>
                                </td>
                                <td class="py-3 px-6 text-left max-w-sm truncate">
                                    <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}">{{ $task->name }}</a>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    @if(isset($task->assigner))
                                    <span>{{ $task->assigner->name }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span>{{ $task->created_at->format('Y/m/d') }}</span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    @if(isset($task->due_date))
                                    <span>{{ $task->due_date->format('Y/m/d') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span>{{ $task->updated_at->format('Y/m/d') }}</span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span>{{ $task->user->name }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex justify-start p-2">
                        {{ $tasks->appends(request()->query())->links() }}
                    </div>
                    @endif
            </div>
        </form>
    </div>
</x-app-layout>
