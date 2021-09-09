<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Create') }}
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
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <!-- Validation Errors -->
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
                <x-link-button class="m-2" :href="route('projects.index')">
                    {{ __('Create Cancel') }}
                    </x-button>
                    <x-button class="m-2 px-10">
                        {{ __('Create') }}
                    </x-button>
            </div>

            <div class="flex flex-col px-8 pt-6 mx-6 rounded-md bg-white">
                <div class="md:flex -mx-3 mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="key" :value="__('Project Key')" class="{{ $errors->has('key') ? 'text-red-600' :'' }}" />
                        <x-input id="key" class="block mt-1 w-full {{ $errors->has('key') ? 'border-red-600' :'' }}" type="text" name="key" :value="old('key')" placeholder="PROJECT_KEY" required autofocus />
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="name" :value="__('Project Name')" class="{{ $errors->has('name') ? 'text-red-600' :'' }}" />
                        <x-input id="name" class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-600' :'' }}" type="text" name="name" :value="old('name')" placeholder="プロジェクト名" required autofocus />
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
