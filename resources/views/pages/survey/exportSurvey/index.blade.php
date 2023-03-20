@extends('templates/dashboard')

@section('title-tab')
    Export Survey
@endsection

@section('title')
    Export Survey
@endsection

@section('subTitle')
    Dashboard
@endsection

@push('style')
    <style>
        .form-group {
            padding-left: 0px;
            padding-right: 0px;
        }

        .form-check-input {
            margin-top: .3rem;
            margin-left: 0rem;
        }

        input.text-jawaban:disabled {
            background-color: white !important;
            opacity: 1 !important;
            border: 0px solid black;
        }
    </style>
@endpush

@section('content')
    <form action="" method="GET">
        {{-- @csrf --}}
        <div class="row">
            <div class="col-lg-4">
                @component('components.formGroup.select', [
                    'label' => 'Pilih Nama Survey',
                    'name' => 'nama_survey_id',
                    'id' => 'nama_survey_id',
                    'class' => 'select2 filter',
                ])
                    @slot('options')
                        @if (count($namaSurvey) > 0)
                            @foreach ($namaSurvey as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }} | {{ $row->tipe }}</option>
                            @endforeach
                        @endif
                    @endslot
                @endcomponent
            </div>
            @if (in_array(Auth::user()->role, ['Admin', 'Institusi', 'Sub Admin']))
                @if (in_array(Auth::user()->role, ['Admin', 'Sub Admin']))
                    <div class="col-lg-4">
                        @component('components.formGroup.select', [
                            'label' => 'Pilih Institusi',
                            'name' => 'institusi_id',
                            'id' => 'institusi_id',
                            'class' => 'filter select2',
                        ])
                            @slot('options')
                                <option value="semua">Semua</option>
                                @if (count($institusi) > 0)
                                    @foreach ($institusi as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                @endif
                            @endslot
                        @endcomponent
                    </div>
                @endif
                <div class="col-lg-4">
                    @component('components.formGroup.select', [
                        'label' => 'Pilih Supervisor / DPL',
                        'name' => 'supervisor_id',
                        'id' => 'supervisor_id',
                        'class' => 'filter select2',
                    ])
                        @slot('options')
                        @endslot
                    @endcomponent
                </div>
            @endif
            @if (in_array(Auth::user()->role, ['Supervisor', 'Institusi']))
                <div class="col-lg-4">
                    @component('components.formGroup.select', [
                        'label' => 'Pilih Surveyor',
                        'name' => 'surveyor_id',
                        'id' => 'surveyor_id',
                        'class' => 'filter select2',
                    ])
                        @slot('options')
                            <option value="semua">Semua</option>
                            @if (count($surveyor) > 0)
                                @foreach ($surveyor as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama_lengkap }}</option>
                                @endforeach
                            @endif
                        @endslot
                    @endcomponent
                </div>
            @endif
            <div class="col-lg-4 col-md-4">
                {{-- Kabupaten / Kota --}}
                @component('components.formGroup.select', [
                    'label' => 'Kabupaten / Kota',
                    'name' => 'kabupaten_kota_id',
                    'id' => 'kabupaten-kota',
                    'class' => 'select2 filter',
                    'options' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-4 col-md-4">
                {{-- Kecamatan --}}
                @component('components.formGroup.select', [
                    'label' => 'Kecamatan',
                    'name' => 'kecamatan_id',
                    'id' => 'kecamatan',
                    'class' => 'select2 filter',
                    'options' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-4 col-md-4">
                {{-- Desa / Kelurahan --}}
                @component('components.formGroup.select', [
                    'label' => 'Desa / Kelurahan',
                    'name' => 'desa_kelurahan_id',
                    'id' => 'desa-kelurahan',
                    'class' => 'select2 filter',
                    'options' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-12 col-md-12">
                {{-- Desa / Kelurahan --}}
                @component('components.formGroup.select', [
                    'label' => 'Lokasi / Posko',
                    'name' => 'lokasi_survey_id',
                    'id' => 'lokasi-survey',
                    'class' => 'select2 filter',
                    'options' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-6 col-md-6">
                @component('components.formGroup.input', [
                    'label' => 'Tanggal Dari (Tanggal-Bulan-Tahun)',
                    'type' => 'text',
                    'name' => 'tanggal_dari',
                    'id' => 'tanggal_dari',
                    'class' => 'filter tanggal',
                    'placeholder' => '',
                    'value' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-6 col-md-6">
                @component('components.formGroup.input', [
                    'label' => 'Tanggal Sampai (Tanggal-Bulan-Tahun)',
                    'type' => 'text',
                    'name' => 'tanggal_sampai',
                    'id' => 'tanggal_sampai',
                    'class' => 'filter tanggal',
                    'placeholder' => '',
                    'value' => '',
                ])
                @endcomponent
            </div>
            <div class="col-lg-12">
                @if (count($errors) > 0)
                    @foreach ($errors->all() as $error)
                        <p class='my-0'>
                            <span class="text-danger error-text">{{ $error }}</span>
                        </p>
                    @endforeach
                @endif
                @if (session('error'))
                    <p class='my-0'>
                        <span class="text-danger error-text">{{ session('error') }}</span>
                    </p>
                @endif
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary mr-3" name="cari">Cari</button>
                <button type="button" class="btn btn-success" id="btn-export">Export</button>
            </div>



        </div>


    </form>

    <form action="{{ url('/exportSurvey/exportExcel') }}" method="GET" id="form-export" class="d-none">
        {{-- @csrf --}}
        <input type="text" name="filter_nama_survey" value="{{ $_GET['nama_survey_id'] ?? '' }}">
        <input type="text" name="filter_institusi" value="{{ $_GET['institusi_id'] ?? '' }}">
        <input type="text" name="filter_supervisor" value="{{ $_GET['supervisor_id'] ?? '' }}">
        <input type="text" name="filter_surveyor" value="{{ $_GET['surveyor_id'] ?? '' }}">
        <input type="text" name="filter_kabupaten_kota_id" value="{{ $_GET['kabupaten_kota_id'] ?? '' }}">
        <input type="text" name="filter_kecamatan_id" value="{{ $_GET['kecamatan_id'] ?? '' }}">
        <input type="text" name="filter_desa_kelurahan_id" value="{{ $_GET['desa_kelurahan_id'] ?? '' }}">
        <input type="text" name="filter_lokasi_survey_id" value="{{ $_GET['lokasi_survey_id'] ?? '' }}">
        <input type="text" name="filter_tanggal_dari" value="{{ $_GET['tanggal_dari'] ?? '' }}">
        <input type="text" name="filter_tanggal_sampai" value="{{ $_GET['tanggal_sampai'] ?? '' }}">
        <input type="text" name="page" value="{{ $_GET['page'] ?? '' }}">
    </form>

    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Institusi</th>
                    <th scope="col">Supervisor / DPL</th>
                    <th scope="col">Lokasi Survey</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $skipped = $data->currentPage() * $data->perPage() - $data->perPage();
                @endphp
                @forelse ($data as $row)
                    <tr>
                        <td>{{ $skipped + $i }}
                            @php
                                $i++;
                            @endphp</td>
                        <td>
                            <h6 class="text-uppercase mb-1 mt-4">Surveyor: {{ $row->profile->nama_lengkap }}</h6>
                            <h6 class="text-uppercase fw-bold mb-0">Responden: {{ $row->responden->kartu_keluarga }}</h6>
                            <span class="text-muted mb-4">Judul: {{ $row->namaSurvey->nama }} </span>
                        </td>
                        <td>{{ $row->profile->institusi->nama }}</td>
                        <td>
                            @php
                                if (count($row->supervisor)) {
                                    $daftarSupervisor = '';
                                    foreach ($row->supervisor as $supervisor) {
                                        $daftarSupervisor .= '<p> - ' . $supervisor->profileSupervisor->nama_lengkap . '</p>';
                                    }
                                } else {
                                    $daftarSupervisor = '-';
                                }
                                echo $daftarSupervisor;
                            @endphp
                        </td>
                        <td>
                            @php
                                $lokasiSurvey = '';
                                if ($row->profile->anggotaSupervisor) {
                                    $lokasiSurvey .=
                                        '<p class="mb-0 pb-0">' .
                                        $row->profile->anggotaSupervisor->lokasiSurveySupervisor->lokasiSurvey->nama_lokasi_survey .
                                        '</p>
                                            <h6 class="fw-bold">' .
                                        $row->profile->anggotaSupervisor->lokasiSurveySupervisor->lokasiSurvey->desa_kelurahan->nama .
                                        '</h6>';
                                } else {
                                    $lokasiSurvey .= '-';
                                }
                                echo $lokasiSurvey;
                            @endphp
                        </td>
                        <td>
                            @php
                                if ($row->namaSurvey->tipe == 'Pre') {
                                    echo '<span class="badge badge-primary">PRE</span>';
                                } else {
                                    echo '<span class="badge badge-success">POST</span>';
                                }
                            @endphp
                        </td>
                        <td>
                            {{ Carbon\Carbon::parse($row->updated_at)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Data Tidak Ada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center col-12">
            {{ $data->links() }}
        </div>
    </div>
@endsection

@push('script')
    <script>
        getKabupaten();
        var idInstitusi = '';

        $('#nama_survey_id').val("{{ $_GET['nama_survey_id'] ?? '' }}").trigger('change');
        $('#tanggal_dari').val("{{ $_GET['tanggal_dari'] ?? '' }}").trigger('change');
        $('#tanggal_sampai').val("{{ $_GET['tanggal_sampai'] ?? '' }}").trigger('change');

        if ("{{ Auth::user()->role }}" == "Admin" || "{{ Auth::user()->role }}" == "Sub Admin") {
            $('#institusi_id').val("{{ $_GET['institusi_id'] ?? '' }}").trigger('change');
        }

        if ("{{ Auth::user()->role }}" == "Admin" || "{{ Auth::user()->role }}" == "Sub Admin") {
            idInstitusi = $('#institusi_id').val();
        } else {
            idInstitusi = "{{ Auth::user()->profile->institusi_id }}";
        }

        setTimeout(
            function() {
                getSupervisor(idInstitusi);
            }, 1000);

        $('#institusi_id').change(function() {
            if ("{{ Auth::user()->role }}" == "Admin" || "{{ Auth::user()->role }}" == "Sub Admin") {
                var idInstitusi = $(this).val();
            }
            getSupervisor(idInstitusi);
        })

        function getSupervisor(idInstitusi) {
            $('#supervisor_id').html('');
            $.ajax({
                url: "{{ url('list/supervisor') }}",
                type: 'GET',
                data: {
                    '_token': '{{ csrf_token() }}',
                    idInstitusi: idInstitusi
                },
                success: function(response) {
                    if (response.length > 0) {
                        $('#supervisor_id').append("<option></option>");
                        $('#supervisor_id').append("<option value='semua'>Semua</option>");
                        for (var i = 0; i < response.length; i++) {
                            $('#supervisor_id').append('<option value="' + response[i].id + '">' +
                                response[
                                    i]
                                .nama_lengkap +
                                '</option>');
                        }
                    }
                }
            })
        }

        setTimeout(
            function() {
                $('#supervisor_id').val("{{ $_GET['supervisor_id'] ?? '' }}").trigger('change');
            }, 2000);

        $('.select2').change(function() {
            $('.error-text').text('');
        })

        $('#btn-export').click(function() {
            $('#form-export').submit();
        });


        function getKabupaten() {
            $("#kabupaten-kota").html('');
            $("#kabupaten-kota").append('<option value="">- Pilih Salah Satu -</option>');
            $('#kabupaten-kota').append("<option value='semua'>Semua</option>");
            $.get("{{ route('listKabupatenKota') }}", {
                idProvinsi: 72,
                is_responden: '{{ $is_responden ?? 2 }}',
            }, function(result) {
                $.each(result, function(key, val) {
                    $('#kabupaten-kota').append(
                        `<option value="${val.id}">${val.nama}</option>`);
                })
                $('#kabupaten-kota').attr('disabled', false);
                $('#kabupaten-kota').val("{{ $_GET['kabupaten_kota_id'] ?? '' }}").trigger('change');
            });
        }

        $("#kabupaten-kota").change(function() {
            if ($("#kabupaten-kota").val() != '') {
                $("#kecamatan").html('');
                $("#kecamatan").append('<option value="">- Pilih Salah Satu -</option>');
                $('#kecamatan').append("<option value='semua'>Semua</option>");
                $.get("{{ route('listKecamatan') }}", {
                    idKabupatenKota: $("#kabupaten-kota").val(),
                    is_responden: '{{ $is_responden ?? 2 }}',
                }, function(result) {
                    $.each(result, function(key, val) {
                        $('#kecamatan').append(
                            `<option value="${val.id}">${val.nama}</option>`);
                    })
                    $('#kecamatan').attr('disabled', false);
                    $('#kecamatan').val("{{ $_GET['kecamatan_id'] ?? '' }}").trigger('change');
                });
            }
        });

        $("#kecamatan").change(function() {
            if ($("#kecamatan").val() != '') {
                $("#desa-kelurahan").html('');
                $("#desa-kelurahan").append('<option value="">- Pilih Salah Satu -</option>');
                $('#desa-kelurahan').append("<option value='semua'>Semua</option>");
                $.get("{{ route('listDesaKelurahan') }}", {
                    idKecamatan: $("#kecamatan").val(),
                    is_responden: '{{ $is_responden ?? 2 }}',
                }, function(result) {
                    $.each(result, function(key, val) {
                        $('#desa-kelurahan').append(
                            `<option value="${val.id}">${val.nama}</option>`);
                    })
                    $('#desa-kelurahan').attr('disabled', false);
                    $('#desa-kelurahan').val("{{ $_GET['desa_kelurahan_id'] ?? '' }}").trigger('change');
                });
            }
        });

        $("#desa-kelurahan").change(function() {
            if ($("#desa-kelurahan").val() != '') {
                $("#lokasi-survey").html('');
                $("#lokasi-survey").append('<option value="">- Pilih Salah Satu -</option>');
                $('#lokasi-survey').append("<option value='semua'>Semua</option>");
                $.get("{{ url('list/lokasi-survey') }}", {
                    idDesaKelurahan: $("#desa-kelurahan").val(),
                    is_responden: '{{ $is_responden ?? 2 }}',
                }, function(result) {
                    $.each(result, function(key, val) {
                        $('#lokasi-survey').append(
                            `<option value="${val.id}">${val.nama_lokasi_survey}</option>`);
                    })
                    $('#lokasi-survey').attr('disabled', false);
                    $('#lokasi-survey').val("{{ $_GET['lokasi_survey_id'] ?? '' }}").trigger('change');
                });
            }
        });
    </script>
@endpush
