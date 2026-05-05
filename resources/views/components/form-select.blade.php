{{--
    Componente: <x-form-select>
    Props:
      name        string   — nombre del campo HTML
      options     array    — [['value' => '', 'label' => ''], ...]
      selected    mixed    — valor seleccionado actualmente
      placeholder string   — texto cuando no hay selección
      required    bool
      error       bool     — muestra borde rojo
      hint        string   — texto de ayuda bajo el select
--}}
@props([
    'name',
    'options'     => [],
    'selected'    => '',
    'placeholder' => '— Selecciona —',
    'required'    => false,
    'error'       => false,
    'hint'        => null,
])

@php
    $uid          = 'fsel_' . uniqid();
    $optionsJson  = json_encode(array_values((array) $options));
    $selectedJson = json_encode((string) $selected);
@endphp

<div x-data="{
        open: false,
        selected: {{ $selectedJson }},
        options: {{ $optionsJson }},
        placeholder: @js($placeholder),
        uid: '{{ $uid }}',

        get selectedLabel() {
            const opt = this.options.find(o => String(o.value) === String(this.selected));
            return opt ? opt.label : this.placeholder;
        },

        rect: {},
        toggle() {
            if (this.open) { this.open = false; return; }
            this.rect = this.$refs.trigger.getBoundingClientRect();
            this.open = true;
            this.$nextTick(() => this.reposition());
        },
        reposition() {
            const el = document.getElementById(this.uid);
            if (!el) return;
            const r      = this.$refs.trigger.getBoundingClientRect();
            const spaceB = window.innerHeight - r.bottom - 8;
            const spaceT = r.top - 8;
            const height = Math.min(el.scrollHeight, 240);

            if (spaceB >= height || spaceB >= spaceT) {
                el.style.top    = (r.bottom + window.scrollY + 4) + 'px';
            } else {
                el.style.top    = (r.top + window.scrollY - height - 4) + 'px';
            }
            el.style.left  = (r.left  + window.scrollX) + 'px';
            el.style.width = r.width + 'px';
        },
        select(value) { this.selected = value; this.open = false; },
    }"
    @keydown.escape.window="open = false"
    @scroll.window="open && reposition()"
    @resize.window="open && reposition()"
    class="relative">

    {{-- Trigger --}}
    <button type="button"
            x-ref="trigger"
            @click="toggle"
            :aria-expanded="open"
            :class="[
                '{{ $error ? 'border-red-400 focus:border-red-500 focus:ring-red-400/20' : '' }}',
                open ? 'ring-2 ring-brand-500/20 border-brand-500' : '',
            ]"
            class="select w-full text-left flex items-center justify-between gap-2 transition-shadow">
        <span class="truncate"
              :class="selected !== '' && selected !== null ? 'text-slate-900' : 'text-slate-400'"
              x-text="selectedLabel"></span>
        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform duration-150"
             :class="open ? 'rotate-180' : ''"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Hidden input for form submission --}}
    <input type="hidden"
           name="{{ $name }}"
           :value="selected"
           {{ $required ? 'required' : '' }}>

    {{-- Dropdown — teleported to <body> to escape overflow containers --}}
    <template x-teleport="body">
        <div :id="uid"
             x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-y-95 -translate-y-1"
             x-transition:enter-end="opacity-100 scale-y-100 translate-y-0"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-y-100"
             x-transition:leave-end="opacity-0 scale-y-95"
             @click.outside="open = false"
             style="position:absolute;z-index:9999;transform-origin:top"
             class="bg-white border border-slate-200 rounded-xl shadow-xl overflow-y-auto max-h-60 py-1">

            {{-- Opción vacía --}}
            @unless($required)
            <button type="button"
                    @click="select('')"
                    :class="selected === '' ? 'bg-brand-50 text-brand-700' : 'text-slate-400 hover:bg-slate-50'"
                    class="w-full text-left px-4 py-2.5 text-sm transition-colors italic">
                {{ $placeholder }}
            </button>
            @endunless

            <template x-for="opt in options" :key="opt.value">
                <button type="button"
                        @click="select(String(opt.value))"
                        :class="String(selected) === String(opt.value)
                            ? 'bg-brand-50 text-brand-700 font-medium'
                            : 'text-slate-700 hover:bg-slate-50'"
                        class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between gap-3">
                    <span x-text="opt.label" class="truncate"></span>
                    <svg x-show="String(selected) === String(opt.value)"
                         class="w-4 h-4 text-brand-600 flex-shrink-0"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </template>
        </div>
    </template>

    @if($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
