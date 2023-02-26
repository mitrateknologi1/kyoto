<button type="submit" class="btn btn-success" id="{{ $id ?? '' }}">
    {{ $label ?? null }} {!! $class != 'simpan' ? '<i class="fas fa-arrow-right"></i>' : '<i class="fas fa-save"></i>' !!}</button>
