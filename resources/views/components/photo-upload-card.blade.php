@props([
    'field',
    'title',
    'description' => null,
    'buttonText' => 'Ambil Foto',
    'existingPath' => null,
    'hero' => false,
])

@php
    $hasPhoto = filled($existingPath);
    $previewId = 'preview-' . str_replace('_', '-', $field);
    $fileNameId = 'name-' . str_replace('_', '-', $field);
    $captureId = 'capture-' . $field;
    $statusId = 'status-' . $field;
@endphp

<div {{ $attributes->class($hero ? '' : 'p-4') }}>
    <div class="mb-3 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <label for="{{ $field }}" class="block text-sm font-semibold text-slate-800">
                {{ $title }}
            </label>

            @if($description)
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    {{ $description }}
                </p>
            @endif
        </div>

        <span
            id="{{ $statusId }}"
            class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $hasPhoto ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}"
        >
            {{ $hasPhoto ? '✓ Ada' : 'Belum' }}
        </span>
    </div>

    <label
        id="{{ $captureId }}"
        for="{{ $field }}"
        class="{{ $hasPhoto ? 'hidden' : 'flex' }} {{ $hero ? 'min-h-24 flex-col' : 'h-14' }} cursor-pointer items-center justify-center gap-2 rounded-xl border {{ $hero ? 'border-2 border-dashed border-blue-300' : 'border-blue-200' }} bg-blue-50 px-4 text-center font-semibold text-blue-700 transition active:scale-[0.99] active:bg-blue-100"
    >
        <span class="{{ $hero ? 'text-3xl' : 'text-xl' }}">📷</span>
        <span>{{ $buttonText }}</span>

        @if($hero)
            <span class="text-xs font-normal text-blue-600">
                Kamera belakang akan dibuka
            </span>
        @endif
    </label>

    <input
        id="{{ $field }}"
        type="file"
        name="{{ $field }}"
        accept="image/*"
        capture="environment"
        class="sr-only"
        data-existing="{{ $hasPhoto ? '1' : '0' }}"
        onchange="previewSelectedPhoto(this)"
    >

    <div
        id="{{ $previewId }}"
        class="mt-3 {{ $hasPhoto ? '' : 'hidden' }} overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
    >
        <img
            src="{{ $hasPhoto ? asset('storage/' . $existingPath) : '' }}"
            alt="Preview {{ $title }}"
            class="max-h-72 w-full object-contain"
        >

        <div class="border-t border-slate-200 p-3">
            <p id="{{ $fileNameId }}" class="truncate text-xs text-slate-600">
                {{ $hasPhoto ? 'Foto tersimpan' : '' }}
            </p>

            <button
                type="button"
                onclick="document.getElementById('{{ $field }}').click()"
                class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white text-sm font-bold text-blue-700 transition active:bg-blue-50"
            >
                📷 Ambil Ulang
            </button>
        </div>
    </div>
</div>
