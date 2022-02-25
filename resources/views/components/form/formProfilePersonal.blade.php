<form id="{{$form_id}}" action="#" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- @if(isset($method) && $method == 'PUT')
        @method('PUT')
    @endif --}}

    <div class="row">
        <div class="col-lg col-md">
            {{-- Nama Lengkap--}}
            @component('components.formGroup.input', ['label' => 'Nama Lengkap', 'type' => 'text', 'class' => '', 'id' => 'nama-lengkap', 'name' => 'nama_lengkap', 'placeholder' => 'Masukkan', 'value' => $profile->nama_lengkap ?? null])
            @endcomponent
        </div>
        <div class="col-lg-4 col-md-4">
            {{-- Jenis Kelamin --}}
            <div class="form-group">
                <label>Jenis Kelamin</label><br>
                @component('components.formGroup.radio', ['label' => 'Laki-laki', 'id' => 'laki-laki', 'name' => 'jenis_kelamin', 'value' => 'Laki-laki', 'checked' => $profile->jenis_kelamin ?? null])
                @endcomponent
                @component('components.formGroup.radio', ['label' => 'Perempuan', 'id' => 'perempuan', 'name' => 'jenis_kelamin', 'value' => 'Perempuan', 'checked' => $profile->jenis_kelamin ?? null])
                @endcomponent
                <div>
                    <span class="text-danger error-text jenis_kelamin-error"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg col-md">
            {{-- Tempat Lahir --}}
            @component('components.formGroup.input', ['label' => 'Tempat Lahir', 'type' => 'text', 'class' => '', 'id' => 'tempat-lahir', 'name' => 'tempat_lahir', 'placeholder' => 'Masukkan', 'value' => $profile->tempat_lahir ?? null])
            @endcomponent
        </div>
        <div class="col-lg col-md">
            {{-- Tanggal Lahir --}}
            @component('components.formGroup.input', ['label' => 'Tanggal Lahir (contoh: 31-12-1992)', 'type' => 'text', 'class' => 'tanggal', 'id' => 'tanggal-lahir', 'name' => 'tanggal_lahir', 'placeholder' => 'Masukkan', 'value' => $profile->tanggal_lahir ?? null])
            @endcomponent
        </div>
    </div>

    {{-- alamat --}}
    @component('components.formGroup.textArea', ['label' => 'Alamat', 'class' => '', 'id' => 'alamat', 'name' => 'alamat', 'placeholder' => 'Masukkan', 'value' => $profile->alamat ?? null])
    @endcomponent

    <div class="row">
        <div class="col-lg col-md">
            {{-- Provinsi --}}
            @component('components.formGroup.select', [
                'label' => 'Provinsi',
                'name' => 'provinsi',
                'id' => 'provinsi',
                'class' => 'select2',
                'options' => '',
            ])  
            @endcomponent
        </div>
        <div class="col-lg col-md">
            {{-- Kabupaten / Kota --}}
            @component('components.formGroup.select', [
                'label' => 'Kabupaten / Kota',
                'name' => 'kabupaten_kota',
                'id' => 'kabupaten-kota',
                'class' => 'select2',
                'options' => '',
            ])  
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-lg col-md">
            {{-- Kecamatan --}}
            @component('components.formGroup.select', [
                'label' => 'Kecamatan',
                'name' => 'kecamatan',
                'id' => 'kecamatan',
                'class' => 'select2',
                'options' => '',
            ])  
            @endcomponent
        </div>
        <div class="col-lg col-md">
            {{-- Desa / Kelurahan --}}
            @component('components.formGroup.select', [
                'label' => 'Desa / Kelurahan',
                'name' => 'desa_kelurahan',
                'id' => 'desa-kelurahan',
                'class' => 'select2',
                'options' => '',
            ])  
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-lg col-md">
            {{-- Nomor HP --}}
            @component('components.formGroup.input', ['label' => 'Nomor HP', 'type' => 'text', 'class' => 'angka', 'id' => 'nomor-hp', 'name' => 'nomor_hp', 'placeholder' => 'Masukkan', 'value' => $profile->nomor_hp ?? null])
            @endcomponent
        </div>
        <div class="col-lg col-md">
            {{-- Email --}}
            @component('components.formGroup.input', ['label' => 'Email (optional)', 'type' => 'text', 'class' => '', 'id' => 'email', 'name' => 'email', 'placeholder' => 'Masukkan', 'value' => $profile->email ?? null])
            @endcomponent
        </div>
    </div>

    {{-- Submit --}}
    <div class="form-row mt-2">
        <div class="form-group ml-auto">
            @component('components.buttons.submit', ['label' => 'Simpan'])
            @endcomponent
        </div>
    </div>
</form>

@component('components.wilayah.form', [
    'provinsi' => $profile->provinsi ?? null, 
    'kabupaten_kota' => $profile->kabupaten_kota ?? null, 
    'kecamatan' => $profile->kecamatan ?? null, 
    'desa_kelurahan' => $profile->desa_kelurahan ?? null])
@endcomponent

@push('script')
<script>
    $(function() {
        if('{{$method}}' == 'PUT') {
            $('#user-id').attr('disabled', true);
        }

        $('#{{$form_id}}').submit(function(e) {
            $("#overlay").fadeIn(100);
            e.preventDefault();
            $('.error-text').text('');
            var formData = new FormData(this)
            $.ajax({
                type: "POST",
                url: "{{$action}}",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                data: formData,
                cache : false,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data);
                    $("#overlay").fadeOut(100);
                    if ($.isEmptyObject(data.error)) {
                        if('{{$method}}' == 'PUT') {
                            swal({
                                title: "Berhasil!",
                                text: "Perubahan berhasil disimpan!",
                                icon: "success",
                                button: false
                            })
                        } else{
                            swal({
                                title: "Berhasil!",
                                text: "Data berhasil disimpan!",
                                icon: "success",
                                button: false
                            })
                        }
                        setTimeout(
                        function () {
                            window.location.href = "{{$back_url}}";
                        }, 2000);
                    
                    }
                    else{
                        swal({
                            title: "Terjadi Kesalahan!",
                            text: "Data gagal disimpan, silahkan cek kembali inputan anda.",
                            icon: "error",
                        });
                        printErrorMsg(data.error);
                    }
                },
            })

        })

        const printErrorMsg = (msg) => {
            $.each(msg, function (key, value) {
                $('.' + key + '-error').text(value);
            });
        }

       
    });

</script>
@endpush