@props(['error'])

@if ($error)
    <div {{ $attributes->merge(['class' => 'font-bold text-md text-red-600']) }}>
        {{ $error }}
    </div>
@endif
