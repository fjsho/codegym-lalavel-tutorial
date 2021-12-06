@section('script')
<script>
// モーダルウィンドウに係る処理
    function toggleModal(event) {
        const body = document.querySelector('body');
        const modal = document.querySelectorAll('.modal');

        //イベントのタイプがキーダウンだった場合とクリックだった場合で処理を分岐させる
        const dataModalSelect = event.type === 'click' ?
            //クリック：ボタンのdata-modal-select属性の値を取得
            event.currentTarget.getAttribute('data-modal-select') :
            //クリック以外（keydownを想定）：展開中のモーダルのdata-modal属性の値を取得
            document.querySelector('.modal:not(.opacity-0)' ).getAttribute('data-modal');

        //各モーダルに対して判定を行う
        for(let i = 0; i < modal.length; i++){
            //modalのdata-modal属性とボタンのdata-modal属性が一致したらメイン処理を実行
            if(modal[i].getAttribute('data-modal') === dataModalSelect){
                //メイン処理
                //modalウィンドウの表示・非表示を切り替える
                modal[i].classList.toggle('opacity-0');
                //modalウィンドウのマウスイベントの有効・無効を切り替える
                modal[i].classList.toggle('pointer-events-none');
                //modal-activeクラスのオンオフを切り替える
                body.classList.toggle('modal-active');
            }
        }
    };

    //modalウィンドウ表示時の背景
    const overlay = document.querySelectorAll('.modal-overlay');
    //背景をクリックするとモーダルが見えなくなる
    for (var i = 0; i < overlay.length; i++) {
        overlay[i].addEventListener('click', toggleModal);
    }

    //モーダルを閉じる（非表示にする）ボタン。複数あるためAllで取得。
    var closeModal = document.querySelectorAll('.modal-close');
    //それぞれの閉じるボタンに処理を付加するための記述。各閉じるボタンにクリックイベントを付加している。
    for (var i = 0; i < closeModal.length; i++) {
        closeModal[i].addEventListener('click', toggleModal);
    }

    //モーダルを表示するボタン。複数あるためAllで取得
    var openModal = document.querySelectorAll('.modal-open');
    //それぞれの表示ボタンに処理を付加するための記述。各表示ボタンにクリックイベントを付加している。
    for (var i = 0; i < openModal.length; i++) {
        openModal[i].addEventListener('click', function(event) {
            //クリックイベントをキャンセル（削除処理をキャンセルしている）
            event.preventDefault();
            //モーダルウィンドウを表示
            toggleModal(event);
        })
    }

    //Escボタンを押した時の処理（モーダルウィンドウを非表示）
    //何かしらキーを押したら発火
    document.onkeydown = function(evt) {
        //もしイベントが空だったらwindow.eventを入れる
        evt = evt || window.event;
        var isEscape = false;
        //Escキーが押された場合にisEscape変数をtrueにする処理
        if ('key' in evt) {
            //もしevt配列の"key"キーがtrueなら
            //"key"キーが"Escape"か"Esc"かどうかを確認し、どっちかならisEscape変数にその値を代入
            isEscape = (evt.key === 'Escape' || evt.key === 'Esc');
        } else {
            //"key"キーがfalseだったら
            //"keyCode"キーに27を代入
            isEscape = (evt.keyCode === 27);
        }
        //isEscapeがtrue かつ bodyタグのクラスに'modal-active'があったらtoggleModalを呼び出す。
        if (isEscape && document.body.classList.contains('modal-active')) {
            toggleModal(evt);
        }
    };


//ファイルアップロードに関する処理の記述
    // ドラッグ&ドロップエリアの取得
    let fileArea = document.getElementById('dropArea');

    // input[type=file]の取得
    let fileInput = document.getElementById('file');

//ここから下のドラッグオーバー・ドラッグアウト・ドロップ時の処理はtoggleでまとめられそう
    // ドラッグオーバー時の処理
    fileArea.addEventListener('dragover', function(e){
        e.preventDefault();
        fileArea.classList.add('dragover');
    });

    // ドラッグアウト時の処理
    fileArea.addEventListener('dragleave', function(e){
        e.preventDefault();
        fileArea.classList.remove('dragover');
    });

    // ドロップ時の処理
    fileArea.addEventListener('drop', function(e){
        e.preventDefault();
        fileArea.classList.remove('dragover');

        // ドロップしたファイルの取得
        const files = e.dataTransfer.files;

        // 取得したファイルをinput[type=file]へ
        fileInput.files = files;
        
        if(typeof files[0] !== 'undefined') {
            // ファイルが正常に受け取れた際の処理
            document.forms[uploadform].submit();
        } else {
            // ファイルが受け取れなかった際の処理
        }
    });

    // input[type=file]に変更があれば実行
    // もちろんドロップ以外でも発火します
    fileInput.addEventListener('change', function(e){
        var file = e.target.files[0];
        
        if(typeof e.target.files[0] !== 'undefined') {
            // ファイルが正常に受け取れた際の処理
            document.forms['uploadform'].submit();
        } else {
            // ファイルが受け取れなかった際の処理
        }
    }, false);

</script>
@endsection
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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
                        {{ __('Task Edit') }}
                    </h3>
                </div>
            </div>
        </div>
        <x-flash-message />
        <form method="POST" action="{{ route('tasks.update', ['project' => $project->id, 'task' => $task]) }}">
            @csrf
            @method('PUT')

            <!-- Validation Errors -->
            <x-validation-errors :errors="$errors" />

            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
                <x-link-button class="m-2" :href="route('tasks.index', ['project' => $project->id])">
                    {{ __('Update Cancel') }}
                </x-link-button>
                <x-button class="m-2 px-10">
                    {{ __('Update') }}
                </x-button>
            </div>

            <div class="flex flex-col px-8 pt-6 mx-6 rounded-md bg-white">
                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/2 px-3 mb-6">
                        <x-label for="task_kind_id" :value="__('Task Kind')" class="{{ $errors->has('task_kind_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_kinds" id="task_kind_id" class="block mt-1 w-full {{ $errors->has('task_kind_id') ? 'border-red-600' :'' }}" name="task_kind_id" :value="old('task_kind_id', $task->task_kind_id)" required autofocus />
                    </div>

                    <div class="md:w-full px-3 mb-6">
                        <x-label for="name" :value="__('Task Name')" class="{{ $errors->has('name') ? 'text-red-600' :'' }}" />
                        <x-input id="name" class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-600' :'' }}" type="text" name="name" :value="old('name', $task->name)" placeholder="課題名" required autofocus />
                    </div>
                </div>

                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-full px-3 mb-6">
                        <x-label for="task_detail" :value="__('Task Detail')" class="{{ $errors->has('task_detail') ? 'text-red-600' :'' }}" />
                        <x-textarea id="task_detail" class="block mt-1 w-full {{ $errors->has('task_detail') ? 'border-red-600' :'' }}" type="text" name="task_detail" :value="old('task_detail', $task->task_detail)" placeholder="課題の詳細" autofocus />
                    </div>
                </div>

                <div class="-mx-3 md:flex mb-6">
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="task_status_id" :value="__('Task Status')" class="{{ $errors->has('task_status_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_statuses" id="task_status_id" class="block mt-1 w-full {{ $errors->has('task_status_id') ? 'border-red-600' :'' }}" type="text" name="task_status_id" :value="old('task_status_id', $task->task_status_id)" required autofocus />
                    </div>

                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="assigner_id" :value="__('Assigner')" class="{{ $errors->has('assigner_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$assigners" id="assigner_id" class="block mt-1 w-full {{ $errors->has('assigner_id') ? 'border-red-600' :'' }}" type="text" name="assigner_id" :value="old('assigner_id', $task->assigner_id)" autofocus />
                    </div>
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="task_category_id" :value="__('Task Category')" class="{{ $errors->has('task_category_id') ? 'text-red-600' :'' }}" />
                        <x-select :options="$task_categories" id="task_category_id" class="block mt-1 w-full {{ $errors->has('task_category_id') ? 'border-red-600' :'' }}" type="text" name="task_category_id" :value="old('task_category_id', $task->task_category_id)" autofocus />
                    </div>
                    <div class="md:w-1/4 px-3 mb-6">
                        <x-label for="due_date" :value="__('Due Date')" class="{{ $errors->has('due_date') ? 'text-red-600' :'' }}" />
                        <x-datepicker id="due_date" class="block mt-1 w-full {{ $errors->has('due_date') ? 'border-red-600' :'' }}" type="text" name="due_date" :value="$task->due_date ?? old('due_date')" autofocus />
                    </div>
                </div>
            </div>
        </form>

        {{-- 投稿画像表示欄ここから --}}
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
                @foreach ($task_pictures as $task_picture)
                    @break($loop->iteration > 5)
                    <form class="w-full" name="deleteform" method="POST" action="{{ route('task_pictures.destroy', ['project' => $project->id, 'task' => $task->id, 'task_picture' => $task_picture]) }}">
                        @csrf
                        @method('DELETE')
                        <div class="h-60">
                            <img src="/storage/{{$task_picture->file_path}}" alt="task_picture" class="w-full h-full object-contain">
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
                                            {{ __('Delete') }}
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- 投稿画像表示欄ここまで --}}

        {{-- 画像投稿機能ここから --}}
        <form name="uploadform" method="POST" action="{{ route('task_pictures.store', ['project' => $project->id, 'task' => $task]) }}" enctype="multipart/form-data">
            @csrf
            <!-- ドラッグ&ドロップエリア -->
            <div id="dropArea" class="flex flex-col px-3 py-9 mx-6 my-3 border-4 border-dashed rounded-md">
                {{-- ここ要英語化 --}}
                <p>ファイルをドラッグ＆ドロップするかクリップボードから画像を貼り付けてください　または　
                    <label for="file" class="inline-block p-3 border rounded bg-gray-300">ファイルを選択する</label>
                    <input type="file" name="file" id="file" class="hidden">
                </p>        
            </div>
        </form>
        
        {{-- 画像投稿機能ここまで --}}

        <form name="deleteform" method="POST" action="{{ route('tasks.destroy', ['project' => $project->id, 'task' => $task]) }}">
            @csrf
            @method('DELETE')
            <!-- Navigation -->
            <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-start">
                <x-button class="modal-open m-2 px-10 bg-red-600 text-white hover:bg-red-700 active:bg-red-900 focus:border-red-900 ring-red-300" data-modal-select="modal-1">
                    {{ __('Delete') }}
                </x-button>
            </div>

            <!--Modal-->
            <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center" data-modal="modal-1">
                <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" data-modal-select="modal-1"></div>

                <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">

                    <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50" data-modal-select="modal-1">
                        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                        <span class="text-sm">(Esc)</span>
                    </div>

                    <div class="modal-content py-4 text-left px-6">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold">{{ __('Are you sure you want to delete this task?') }}</p>
                            <div class="modal-close cursor-pointer z-50" data-modal-select="modal-1">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                </svg>
                            </div>
                        </div>

                        <p>{{ __('Are you sure you want to delete this task? Once a task is deleted, all of its resources and data will be permanently deleted.') }}</p>

                        <div class="flex justify-end pt-2">
                            <x-link-button class="modal-close m-2" href="#" data-modal-select="modal-1">
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

        {{-- 作業ここから --}}
        <div>
            {{-- ラベル --}}
            <div class="mx-auto">
                <div class="overflow-hidden sm:rounded-lg">
                    <div class="px-6 py-3">
                        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Comments') }}
                        </h4>
                    </div>
                </div>
            </div>
            {{--投稿済みコメント表示枠 --}}
                @foreach ($task_comments as $task_comment)
                <form name="deleteform" method="POST" action="{{ route('task_comments.destroy', ['project' => $project->id, 'task' => $task->id, 'task_comment' => $task_comment]) }}">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col px-3 pt-3 mx-6 mb-3 rounded-md bg-white">
                        <div class="-mx-3 md:flex">
                            <div class="md:w-1/12 px-3 mb-3">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <div class="md:w-9/12 px-3 mb-3">
                                <p><b>{{$task_comment->user->name}}</b></p>
                                <p>{{$task_comment->created_at}}</p>
                            </div>
                            <!-- Navigation -->
                            <div class="md:w-2/12 px-3 mb-3">
                                <div class="flex justify-end">
                                        @can('delete', $task_comment)
                                        <x-list-button class="modal-open px-8 bg-gray-100 text-red-400 border-red-400 hover:bg-gray-300 active:bg-gray-600 focus:border-red-900 ring-red-300" data-modal-select="modal-2-{{$loop->iteration}}">
                                            {{ __('Delete') }}
                                        </x-list-button>
                                        @endcan
                                </div>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/12 px-3 mb-6">
                            </div>
                            <div class="md:w-11/12 px-3 mb-6 break-all">
                                <p>{!!nl2br(e($task_comment->comment))!!}</p>
                            </div>
                        </div>
                    </div>
                    <!--Modal-->
                    <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center" data-modal="modal-2-{{$loop->iteration}}">
                        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50" data-modal-select="modal-2-{{$loop->iteration}}"></div>
        
                        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        
                            <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50" data-modal-select="modal-2-{{$loop->iteration}}">
                                <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                </svg>
                                <span class="text-sm">(Esc)</span>
                            </div>
        
                            <div class="modal-content py-4 text-left px-6">
                                <div class="flex justify-between items-center pb-3">
                                    <p class="text-2xl font-bold">{{ __('Are you sure you want to delete this comment?') }}</p>
                                    <div class="modal-close cursor-pointer z-50" data-modal-select="modal-2-{{$loop->iteration}}">
                                        <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                        </svg>
                                    </div>
                                </div>
        
                                <p>{{ __('Are you sure you want to delete this comment? Once a comment is deleted, all of its resources and data will be permanently deleted.') }}</p>
        
                                <div class="flex justify-end pt-2">
                                    <x-link-button class="modal-close m-2" href="#" data-modal-select="modal-2-{{$loop->iteration}}">
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
                @endforeach

            {{--新規コメント投稿枠--}}
            <form method="POST" action="{{ route('task_comments.store', ['project' => $project->id, 'task' => $task->id]) }}">
            @csrf

                <div class="flex flex-col mx-6 mt-8 rounded-md bg-white">
                    <div class="-mx-3 md:flex">
                        <div class="md:w-full px-3">
                            <x-textarea id="comment" class="block w-full border-none" type="text" name="comment" rows="4"  :value="old('comment')" placeholder="コメント" autofocus />
                        </div>
                    </div>
                </div>
                <!-- Navigation -->
                <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
                    <x-button class="m-2 px-10">
                        {{ __('Create Comment') }}
                    </x-button>
                </div>
            </form>
        </div>
        {{-- 作業ここまで --}}

    </div>
</x-app-layout>
