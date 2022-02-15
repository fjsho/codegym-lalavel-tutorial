@section('script')
<script>
// モーダルウィンドウの表示・非表示切り替え
function toggleModal(event) {
        const body = document.querySelector('body');
        const modal = document.querySelectorAll('.modal');

        // Escキーでモーダルを閉じる処理に対応するためイベントに応じて格納内容を分岐させた
        // →キーダウンイベントではcurrentTarget.getAttribute()が使えなかったため 
        const dataModalSelect = event.type === 'click' ?
            event.currentTarget.getAttribute('data-modal-select') :
            document.querySelector('.modal:not(.opacity-0)' ).getAttribute('data-modal');

        // メイン処理
        for(let i = 0; i < modal.length; i++){
            if(modal[i].getAttribute('data-modal') === dataModalSelect){
                modal[i].classList.toggle('opacity-0');
                modal[i].classList.toggle('pointer-events-none');
                body.classList.toggle('modal-active');
            }
        }
    };

    // 以下イベントの発火条件設定
    const overlay = document.querySelectorAll('.modal-overlay');
    for (let i = 0; i < overlay.length; i++) {
        overlay[i].addEventListener('click', toggleModal);
    }

    let closeModal = document.querySelectorAll('.modal-close');
    for (let i = 0; i < closeModal.length; i++) {
        closeModal[i].addEventListener('click', toggleModal);
    }

    let openModal = document.querySelectorAll('.modal-open');
    for (let i = 0; i < openModal.length; i++) {
        openModal[i].addEventListener('click', function(event) {
            event.preventDefault();
            toggleModal(event);
        })
    }

    document.onkeydown = function(event) {
        event = event || window.event;
        let isEscape = false;
        if ('key' in event) {
            isEscape = (event.key === 'Escape' || event.key === 'Esc');
        } else {
            isEscape = (event.keyCode === 27);
        }
        if (isEscape && document.body.classList.contains('modal-active')) {
            toggleModal(event);
        }
    };

    // ドラッグ＆ドロップ対応ファイルアップロード
    const fileArea = document.getElementById('dropArea');
    let fileInput = document.getElementById('file');

    function toggleDragOver(event){
        event.preventDefault();
        fileArea.classList.toggle('dragover');
    };

    fileArea.addEventListener('dragover', toggleDragOver);
    fileArea.addEventListener('dragleave', toggleDragOver);

    // ドロップでファイルを選択した場合
    fileArea.addEventListener('drop', function(event){
        toggleDragOver(event);
        const files = event.dataTransfer.files;
        fileInput.files = files;        
        if(typeof files[0] !== 'undefined') {
            document.forms[`uploadform`].submit();
        }
    });

    // クリックしてファイル選択した場合
    fileInput.addEventListener('change', function(event){
        const file = event.target.files[0];
        if(typeof event.target.files[0] !== 'undefined') {
            document.forms[`uploadform`].submit();
        }
    }, false);

</script>
@endsection
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
                        {{ __('Task Create') }}
                    </h3>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('tasks.store', ['project' => $project->id]) }}">
            @csrf
            <!-- Validation Errors -->
            <x-flash-message />
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
                <x-link-button class="m-2" :href="route('tasks.index', ['project' => $project->id])">
                    {{ __('Create Cancel') }}
                    </x-button>
                    <x-button class="m-2 px-10">
                        {{ __('Create') }}
                    </x-button>
            </div>

            <div class="flex flex-col px-8 pt-6 mx-6 rounded-md bg-white">
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6">
                        <x-label for="task_kind_id" :value="__('Task Kind')" class="{{ $errors->has('task_kind_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_kinds" id="task_kind_id" class="block mt-1 w-full {{ $errors->has('task_kind_id') ? 'border-red-600' :'' }}" name="task_kind_id" :value="old('task_kind_id')" required autofocus />
                    </div>

                    <div class="md:w-full px-3 mb-6">
                        <x-label for="name" :value="__('Task Name')" class="{{ $errors->has('name') ? 'text-red-600' :'' }}" />
                        <x-input id="name" class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-600' :'' }}" type="text" name="name" :value="old('name')" placeholder="{{ __('Task Name') }}" required autofocus />
                    </div>
                </div>

                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="task_detail" :value="__('Task Detail')" class="{{ $errors->has('task_detail') ? 'text-red-600' :'' }}" />
                        <x-textarea id="task_detail" class="block mt-1 w-full {{ $errors->has('task_detail') ? 'border-red-600' :'' }}" type="text" name="task_detail" :value="old('task_detail')" placeholder="課題の詳細" autofocus />
                    </div>
                </div>

                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="task_status_id" :value="__('Task Status')" class="{{ $errors->has('task_status_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_statuses" id="task_status_id" class="block mt-1 w-full {{ $errors->has('task_status_id') ? 'border-red-600' :'' }}" type="text" name="task_status_id" :value="old('task_status_id')" required autofocus />
                    </div>

                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="assigner_id" :value="__('Assigner')" class="{{ $errors->has('assigner_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$assigners" id="assigner_id" class="block mt-1 w-full {{ $errors->has('assigner_id') ? 'border-red-600' :'' }}" type="text" name="assigner_id" :value="old('assigner_id')" autofocus />
                    </div>
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="task_category_id" :value="__('Task Category')" class="{{ $errors->has('task_category_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_categories" id="task_category_id" class="block mt-1 w-full {{ $errors->has('task_category_id') ? 'border-red-600' :'' }}" type="text" name="task_category_id" :value="old('task_category_id')" autofocus />
                    </div>
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="due_date" :value="__('Due Date')" class="{{ $errors->has('due_date') ? 'text-red-600' :'' }}" />
                        <x-datepicker id="due_date" class="block mt-1 w-full {{ $errors->has('due_date') ? 'border-red-600' :'' }}" type="text" name="due_date" :value="$errors->has('due_date') ? null : old('due_date')" autofocus />
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 画像表示 -->
    <div class="mx-auto">
        <div class="overflow-hidden sm:rounded-lg">
            <div class="px-6 py-3">
                <h4 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pictures') }}
                </h4>
            </div>
        </div>
    </div>
    <div class="px-3 pt-3 mx-6 mb-3 rounded-md">
        <div class="grid grid-cols-2 gap-10 p-3 mb-6 place-items-center">
            @if(session('tmp_files'))
                @foreach(session('tmp_files') as $name => $path)
                    <form class="w-full" name="deleteform" method="POST" action="{{ route('tasks.destroyTmpPicture', ['project' => $project->id]) }}">
                        @csrf
                        <div class="h-60">
                            <img src="/storage/tmp/{{$path}}" alt="{{$path}}" class="w-full h-full object-contain">
                        </div>
                        <!-- Navigation -->
                        <div class="w-full h-full px-3 py-3">
                            <div class="flex justify-end">
                                <x-list-button class="modal-open px-8 bg-gray-100 text-red-400 border-red-400 hover:bg-gray-300 active:bg-gray-600 focus:border-red-900 ring-red-300" data-modal-select="modal-3-{{$loop->iteration}}">
                                    {{ __('Delete') }}
                                </x-list-button>
                            </div>
                        </div>
                        <!--Modal-->
                        <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center" data-modal="modal-3-{{$loop->iteration}}">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" data-modal-select="modal-3-{{$loop->iteration}}"></div>
            
                            <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            
                                <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50" data-modal-select="modal-3-{{$loop->iteration}}">
                                    <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                    </svg>
                                    <span class="text-sm">(Esc)</span>
                                </div>
            
                                <div class="modal-content py-4 text-left px-6">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold">{{ __('Are you sure you want to delete this picture?') }}</p>
                                        <div class="modal-close cursor-pointer z-50" data-modal-select="modal-3-{{$loop->iteration}}">
                                            <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                                <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                            </svg>
                                        </div>
                                    </div>
            
                                    <p>{{ __('Are you sure you want to delete this picture? Once a picture is deleted, all of its resources and data will be permanently deleted.') }}</p>
            
                                    <div class="flex justify-end pt-2">
                                        <x-link-button class="modal-close m-2" href="#" data-modal-select="modal-3-{{$loop->iteration}}">
                                            {{ __('Cancel') }}
                                        </x-link-button>
                                        <x-button class="m-2 px-10 bg-red-600 text-white hover:bg-red-700 active:bg-red-900 focus:border-red-900 ring-red-300">
                                            <input type="hidden" id="tmp_file_name" name="tmp_file_name" value="{{$name}}">
                                            {{ __('Delete') }}
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
            @endif
        </div>
    </div>

    <!-- 画像投稿 -->
    <form name="uploadform" method="POST" action="{{ route('tasks.storeTmpPicture', ['project' => $project->id]) }}" enctype="multipart/form-data">
        @csrf
        <!-- ドラッグ&ドロップエリア -->
        <div id="dropArea" class="flex flex-col px-3 py-9 mx-6 my-3 border-4 border-dashed rounded-md">
            <p>{{ __('Please drag and drop a file or paste an image from the clipboard or') }}
                <label for="file" class="inline-block p-3 border rounded bg-gray-300">{{ __('File Select') }}</label>
                <input type="file" name="file" id="file" class="hidden">
            </p>        
        </div>
    </form>
</x-app-layout>
