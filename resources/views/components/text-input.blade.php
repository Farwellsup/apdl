@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => ' border border-gray-300  focus:border-indigo-500  rounded-md shadow-sm']) }}>
