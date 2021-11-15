<!--disabled属性の初期値はfalse、options属性とvalue属性の値は呼び出し先の値を使う-->
@props(['disabled' => false, 'options', 'value'])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block appearance-none w-full bg-grey-lighter border border-grey-lighter text-grey-darker py-2 px-4 pr-8 rounded rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="grid-state']) !!}>
    <option {{ ($value === '' or is_null($value)) ? 'selected="selected"' : '' }}></option>
    @foreach($options as $option)
    <option value="{{ $option->id }}" {{ $value == $option->id ? 'selected="selected"' : '' }}>{{ $option->name }}</option>
    @endforeach
</select>
