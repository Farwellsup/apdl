@props(['disabled' => false, 'minlength' => null ])

<input @disabled($disabled) minlength="{{$minlength}}"  {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-gray-200 border border-transparent rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-tertiary focus:border-transparent transition focus:border-tertiary focus:ring-tertiary']) }}>
