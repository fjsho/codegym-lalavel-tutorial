@foreach(['success', 'error'] as $key)
    @if (session($key))
        @switch ($key)
            @case('error')
                @php $color = 'red' @endphp
                @break
            @default
                @php $color = 'green' @endphp
        @endswitch
<div {{ $attributes->merge(['class' => "flex py-3 px-5 mx-6 mt-4 bg-$color-100 text-$color-900 text-sm rounded-md border border-$color-200"]) }} role="alert">
    {{ session($key) }}
</div>
    @endif
@endforeach
