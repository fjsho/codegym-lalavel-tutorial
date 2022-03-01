<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <x-slot name="sidemenu">
        <x-side-menu-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
            {{ __('Projects') }}
        </x-side-menu-link>
        <x-side-menu-link :href="route('projects.create')" :active="request()->routeIs('projects.create')">
            {{ __('Project Create') }}
        </x-side-menu-link>
    </x-slot>

    <div>
        <div class="mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Assigned Tasks') }}
                    </h3>
                </div>
            </div>
        </div>

        <form method="GET" action="{{route('dashboard')}}">
            <!-- Validation Errors -->
            <x-flash-message />
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="flex max-w-full mx-auto px-4 py-6 sm:px-6 lg:px-6">
                <div class="md:w-1/3 px-3 mb-6 mr-6">
                    <x-label for="key" :value="__('Keyword')" class="{{$errors->has('keyword') ? 'text-red-600' :''}}" />
                    <x-input id="keyword" class="block mt-1 w-full $errors->has('keyword') ? 'border-red-600' :''" type="text" name="keyword" :value="$keyword" :placeholder="__('Keyword')" autofocus />
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
                        {{$tasks->appends(request()->query())->links()}}
                    </div>
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 text-sm leading-normal">
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('project.name', __('Project Name'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('id', __('Task Key'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('task_kind.name', __('Task Kind'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('name', __('Task Name'))
                                </th>
                                <th class="py-3 px-6 text-left">
                                    @sortablelink('task_status.name', __('Task Status'))
                                </th>
                                <th class="py-3 px-6 text-center">
                                    @sortablelink('due_date', __('Due Date'))
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($tasks as $task)
                            <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer @if($loop->even)bg-gray-50 @endif" onclick="location.href='{{route('tasks.edit', ['project' => $task->project->id, 'task' => $task->id])}}'">
                                <td class="py-3 px-6 text-left">
                                    <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('projects.edit', ['project' => $task->project->id]) }}">{{ $task->project->name }}</a>
                                </td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('tasks.edit', ['project' => $task->project->id, 'task' => $task->id]) }}">{{ $task->key }}</a>
                                </td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span>{{ $task->task_kind->name }}</span>
                                </td>
                                <td class="py-3 px-6 text-left max-w-sm truncate">
                                    <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('tasks.edit', ['project' => $task->project->id, 'task' => $task->id]) }}">{{ $task->name }}</a>
                                </td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span>{{ $task->task_status->name }}</span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    @if(isset($task->due_date))
                                    <span>{{ $task->due_date->format('Y/m/d') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex justify-start p-2">
                        {{$tasks->appends(request()->query())->links()}}
                    </div>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
