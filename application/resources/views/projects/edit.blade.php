@section('script')
<script>
    function toggleModal() {
        const body = document.querySelector('body');
        const modal = document.querySelector('.modal');
        modal.classList.toggle('opacity-0');
        modal.classList.toggle('pointer-events-none');
        body.classList.toggle('modal-active');
    };

    const overlay = document.querySelector('.modal-overlay');
    overlay.addEventListener('click', toggleModal);

    var closeModal = document.querySelectorAll('.modal-close');
    for (var i = 0; i < closeModal.length; i++) {
        closeModal[i].addEventListener('click', toggleModal);
    }

    var openModal = document.querySelectorAll('.modal-open');
    for (var i = 0; i < openModal.length; i++) {
        openModal[i].addEventListener('click', function(event) {
            event.preventDefault();
            toggleModal();
        })
    }

    document.onkeydown = function(evt) {
        evt = evt || window.event;
        var isEscape = false;
        if ('key' in evt) {
            isEscape = (evt.key === 'Escape' || evt.key === 'Esc');
        } else {
            isEscape = (evt.keyCode === 27);
        }
        if (isEscape && document.body.classList.contains('modal-active')) {
            toggleModal();
        }
    };

</script>
@endsection
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Edit') }}
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
        <x-flash-message />
        <form method="POST" action="{{ route('projects.update', [ 'project' => $project ]) }}">
            @csrf
            @method('PUT')

            <!-- Validation Errors -->
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
                <x-link-button class="m-2" :href="route('projects.index')">
                    {{ __('Update Cancel') }}
                </x-link-button>
                <x-button class="m-2 px-10">
                    {{ __('Update') }}
                </x-button>
            </div>

            <div class="flex flex-col px-8 pt-6 mx-6 rounded-md bg-white">
                <div class="md:flex -mx-3 mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="key" :value="__('Project Key')" class="{{ $errors->has('key') ? 'text-red-600' :'' }}" />
                        <x-input id="key" class="block mt-1 w-full {{ $errors->has('key') ? 'border-red-600' :'' }}" type="text" name="key" :value="old('key', $project->key)" placeholder="PROJECT_KEY" required autofocus />
                    </div>
                </div>
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="name" :value="__('Project Name')" class="{{ $errors->has('name') ? 'text-red-600' :'' }}" />
                        <x-input id="name" class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-600' :'' }}" type="text" name="name" :value="old('name', $project->name)" placeholder="プロジェクト名" required autofocus />
                    </div>
                </div>
            </div>
        </form>

        <form name="deleteform" method="POST" action="{{ route('projects.destroy', [ 'project' => $project ]) }}">
            @csrf
            @method('DELETE')
            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-start">
                <x-button class="modal-open m-2 px-10 bg-red-600 text-white hover:bg-red-700 active:bg-red-900 focus:border-red-900 ring-red-300">
                    {{ __('Delete') }}
                </x-button>
            </div>

            <!--Modal-->
            <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
                <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

                <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">

                    <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
                        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                        <span class="text-sm">(Esc)</span>
                    </div>

                    <div class="modal-content py-4 text-left px-6">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold">{{ __('Are you sure you want to delete this project?') }}</p>
                            <div class="modal-close cursor-pointer z-50">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                </svg>
                            </div>
                        </div>

                        <p>{{ __('Are you sure you want to delete this project? Once a project is deleted, all of its resources and data will be permanently deleted.') }}</p>

                        <div class="flex justify-end pt-2">
                            <x-link-button class="modal-close m-2" href="#">
                                {{ __('Cancel') }}
                            </x-link-button>
                            <x-button class="m-2 px-10 bg-red-600 text-white hover:bg-red-700 active:bg-red-900 focus:border-red-900 ring-red-300">
                                {{ __('Delete') }}
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
