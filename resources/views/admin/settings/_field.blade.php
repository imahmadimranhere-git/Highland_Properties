{{-- One settings input. @include('admin.settings._field', ['key' => 'phone', 'label' => 'Phone', 'type' => 'text']) --}}
@php $type = $type ?? 'text'; @endphp

<div class="form-group">
    <label class="form-label" for="s-{{ $key }}">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea id="s-{{ $key }}" name="{{ $key }}" rows="{{ $rows ?? 4 }}"
                  class="form-control @error($key) is-invalid @enderror">{{ old($key, $values[$key] ?? '') }}</textarea>
    @else
        <input id="s-{{ $key }}" name="{{ $key }}" type="{{ $type }}"
               class="form-control @error($key) is-invalid @enderror"
               value="{{ old($key, $values[$key] ?? '') }}" @isset($placeholder) placeholder="{{ $placeholder }}" @endisset>
    @endif

    @isset($hint)<span class="form-hint">{{ $hint }}</span>@endisset
    @error($key)<span class="form-error">{{ $message }}</span>@enderror
</div>
