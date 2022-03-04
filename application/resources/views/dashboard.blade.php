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
        </form>
    </div>
</x-app-layout>
