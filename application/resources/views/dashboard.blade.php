@section('script')
<script>
    const radioElements = document.getElementsByName('project_id');
    document.addEventListener('DOMContentLoaded', function() {
        for (const radioElement of radioElements) {
            radioElement.addEventListener('change', function(){
                document.forms["filter_search"].submit();
            });
        }
    });

    const ppc = document.getElementById('progress-pie-chart'),
        ppcProgressFill = document.getElementById('ppc-progress-fill'),
        ppcPercents = document.getElementById('ppc-percents'),
        percent = parseInt(ppc.dataset.percent),
        deg = 360 * percent / 100;
    if (percent > 50) {
        ppc.classList.add('gt-50');
    }
    ppcProgressFill.classList.add('transform','rotate('+ deg +'deg)');
    // ppcPercents.textContent = `${percent}%`;

    console.log(ppc);
    console.log(ppcProgressFill);
    console.log(ppcPercents);
    console.log(percent);
    console.log(deg);
</script>
@endsection

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
        <form name="filter_search" method="GET" action="{{route('dashboard')}}">
            <!-- Validation Errors -->
            <x-flash-message />
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="flex">
                <div class="flex flex-col">
                    {{-- 担当者 --}}
                    <div class="flex max-w-full mx-auto px-4 py-6 sm:px-6 lg:px-6">
                        <div class="md:w-1/3 px-3 mb-6 mr-6">
                            <x-label for="assigner_id" :value="__('Assigner')" class="{{ $errors->has('assigner_id') ? 'text-red-600' :'' }}" />
                            <x-select :options="$assigners" id="assigner_id" class="block mt-1 w-full {{ $errors->has('assigner_id') ? 'border-red-600' :'' }}" type="text" name="assigner_id" :value="$assigner_id" autofocus />
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
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer @if($loop->even)bg-gray-50 @endif" onclick="document.getElementById('{{$project->id}}').click()">
                                        <td class="py-3 px-6 text-left">
                                            <p>
                                                <input type="radio" name="project_id" id="{{$project->id}}" value="{{$project->id}}" @if($project->id ==  intval($searched_project_id)) checked @endif/>
                                                <label for="{{$project->id}}">{{ $project->name }}</label>
                                            </p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col">
                    {{-- @next グラフデザイン続き --}}
                    <div id="progress-pie-chart" class="relative mx-6 my-6 w-52 h-52 rounded-full bg-gray-400" data-percent="45">
                        <div id="ppc-progress" class="absolute rounded-full w-52 h-52">
                            <div id="ppc-progress-fill" class="absolute rounded-full w-52 h-52 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-200 rotate-45 bg-clip-content"></div>
                        </div>
                        <div id="ppc-percents" class="absolute rounded-full w-5/6 h-5/6 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white text-center table">
                            <div id="ppc-percents-wrapper" class="table-cell align-middle">
                                <span class="block text-5xl font-bold text-green-300">%</span>
                            </div>
                        </div>
                    </div>
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
