@props([
    'id'          => null,
    'name'        => null,
    'placeholder' => 'Select an option',
    'options'     => [],
    'selected'    => null,
    'required'    => false,
])

@php
    $resolvedName    = $name ?? $id;
    $oldValue        = old($resolvedName, $selected);

    $initialLabel = $placeholder;
    foreach ($options as $val => $label_text) {
        if ((string) $val === (string) $oldValue) {
            $initialLabel = $label_text;
            break;
        }
    }
@endphp

<div
    x-data="{
        open:         false,
        selected:     {{ $oldValue !== null ? "'" . $oldValue . "'" : 'null' }},
        selectedLabel: {{ Js::from($initialLabel) }},
        placeholder:  {{ Js::from($placeholder) }},
        options:      {{ Js::from(collect($options)->map(fn($l, $v) => ['value' => (string)$v, 'label' => $l])->values()) }},

        choose(value, label) {
            this.selected      = value;
            this.selectedLabel = label;
            this.open          = false;
            // dispatch change event so the form can listen
            this.$nextTick(() => {
                this.$el.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        isSelected(value) {
            return this.selected !== null && String(this.selected) === String(value);
        }
    }"
    x-on:keydown.escape="open = false"
    x-on:click.outside="open = false"
    class="relative"
>

    <input type="hidden" name="{{ $resolvedName }}" :value="selected" {{ $required ? 'required' : '' }} />

    <button
        type="button"
        id="{{ $id }}-btn"
        x-on:click="open = !open"
        :aria-expanded="open"
        aria-haspopup="listbox"
        {{ $attributes->merge([
            'class' => 'w-full flex items-center justify-between px-4 py-3 bg-gray-200 border
                        border-transparent rounded-lg text-sm text-left cursor-pointer
                        focus:outline-none focus:ring-2 focus:ring-[#FFB901] focus:border-transparent
                        transition-all duration-150'
        ]) }}
    >
        <span :class="selected === null ? 'text-gray-400' : 'text-gray-700'" x-text="selectedLabel"></span>

        {{-- Animated chevron --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4 text-gray-500 transition-transform duration-200 shrink-0 ml-2"
            :class="open ? 'rotate-180' : 'rotate-0'"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        role="listbox"
        class="absolute z-50 mt-1 w-full origin-top bg-white border border-gray-200
               rounded-lg shadow-lg overflow-hidden"
        style="display: none;"
    >
        {{-- Scrollable options list --}}
        <ul class="max-h-60 overflow-y-auto py-1 divide-y divide-gray-50">
            <template x-for="option in options" :key="option.value">
                <li
                    role="option"
                    :aria-selected="isSelected(option.value)"
                    x-on:click="choose(option.value, option.label)"
                    class="flex items-center justify-between px-4 py-2.5 text-sm cursor-pointer
                           text-gray-700 select-none
                           hover:bg-gray-100 transition-colors duration-100"
                    :class="isSelected(option.value) ? 'font-medium text-gray-900 bg-gray-50' : ''"
                >
                    <span x-text="option.label"></span>

                    {{-- Checkmark (only visible when selected) --}}
                    <svg
                        x-show="isSelected(option.value)"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 shrink-0 ml-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                        style="color: #FFB901;"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </li>
            </template>
        </ul>
    </div>
</div>