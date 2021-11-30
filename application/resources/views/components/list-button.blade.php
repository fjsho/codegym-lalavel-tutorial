<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-50 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-50 active:bg-gray-50 focus:outline-none focus:border-gray-50 focus:ring ring-gray-50 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
