
@extends('templates/dashboard')

@section('title-tab')Tambah Pengguna
@endsection

@section('title')
Tambah Pengguna
@endsection

@section('subTitle')
Pengguna
@endsection

@push('style')

@endpush

@section('content')
<section>
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    @component('components.form.formUser')
                        @slot('id', 'form_add_user')
                        @slot('action', route('user.store'))
                        @slot('method', 'POST')
                        @slot('back', route('user.index'))
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script>

</script>
@endpush
