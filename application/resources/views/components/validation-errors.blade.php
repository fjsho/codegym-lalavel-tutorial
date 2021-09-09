@props(['errors'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'py-3 px-5 mx-6 mt-4 bg-red-100 text-red-900 text-sm rounded-md border border-red-200']) }} role="alert">
        <div>
            {{ __('Whoops! Something went wrong.') }}
        </div>

        <ul class="mt-3 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
