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
        <form method="GET" action="{{route('dashboard')}}">
            <!-- Validation Errors -->
            <x-flash-message />
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="flex">
                <div class="flex flex-col">
                    {{-- 担当者 --}}
                    <div class="flex max-w-full mx-auto px-4 py-6 sm:px-6 lg:px-6">
                        <div class="md:w-1/3 px-3 mb-6 mr-6">
                            <x-label for="assigner" :value="__('Assigner')" class="{{$errors->has('assigner') ? 'text-red-600' :''}}" />
                            <select name="assigner" id="arrigner" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">あなた（{{ Auth::user()->name }}）</option>
                                <option value="">ユーザーA</option>
                                <option value="">ユーザーB</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap content-center">
                            <x-button class="px-10">
                                切り替え
                            </x-button>
                        </div>
                    </div>
                    {{-- プロジェクト --}}
                    <div class="flex flex-col mx-6 mb-6 bg-white rounded h-screen overflow-scroll">
                        @if(0 < $projects->count())
                            <table class="min-w-max w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal sticky top-0">
                                        <th class="py-3 px-6 text-left">
                                            @sortablelink('name', __('Project Name'))
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light">
                                    @foreach($projects as $project)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer @if($loop->even)bg-gray-50 @endif" onclick="location.href='{{ route('projects.edit', ['project' => $project->id]) }}'">
                                        <td class="py-3 px-6 text-left">
                                            <input type="radio" value="{{$project->id}}">
                                            <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('projects.edit', ['project' => $project->id]) }}">{{ $project->name }}</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    {{-- グラフ --}}
                    <div>グラフ枠</div>
                    {{-- タスク --}}
                    <div class="flex flex-col mx-6 mb-6 bg-white rounded h-screen overflow-scroll">
                        @if(0 < $tasks->count())
                            <table class="min-w-max w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-200 text-gray-600 text-sm leading-normal sticky top-0">
                                        <th class="py-3 px-6 text-left">
                                            @sortablelink('task_kind.name', __('Task Kind'))
                                        </th>
                                        <th class="py-3 px-6 text-left">
                                            @sortablelink('name', __('Task Name'))
                                        </th>
                                        <th class="py-3 px-6 text-center">
                                            @sortablelink('due_date', __('Due Date'))
                                        </th>
                                        <th class="py-3 px-6 text-center">
                                            @sortablelink('task_category', __('Task Category'))
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm font-light">
                                    @foreach($tasks as $task)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer @if($loop->even)bg-gray-50 @endif" onclick="location.href='{{ route('tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}'">
                                        <td class="py-3 px-6 text-left whitespace-nowrap">
                                            <span>{{ $task->task_kind->name }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-left max-w-sm truncate">
                                            <a class="underline font-medium text-gray-600 hover:text-gray-900" href="{{ route('tasks.edit', ['project' => $project->id, 'task' => $task->id]) }}">{{ $task->name }}</a>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            @if(isset($task->due_date))
                                            <span>{{ $task->due_date->format('Y/m/d') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-left whitespace-nowrap">
                                            <span>{{ $task->task_category->name }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
