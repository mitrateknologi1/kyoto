<div class="form-group">
    <label for="">{{ $label }}</label>
    <input type="{{ $type }}" class="form-control {{ $class }}" id="{{ $id }}"
        name="{{ $name }}" placeholder="{{ $placeholder }} {{ $label }}" value="{{ $value }}">
    <span class="text-danger error-text {{ $name }}-error"></span>
</div>
