@extends('templates/dashboard')

@section('title-tab')
    Survey
@endsection

@section('title')
    Survey
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
    <section>
        <div class="row">
            <div class="col-lg-4">
                @component('components.formGroup.select', [
                    'label' => 'Pilih Jenis Survey',
                    'name' => 'nama_survey_id',
                    'id' => 'nama_survey_id',
                    'class' => 'select2 filter',
                ])
                    @slot('options')
                        <option value="semua">Semua</option>
                        @if (count($namaSurvey) > 0)
                            @foreach ($namaSurvey as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }} | {{ $row->tipe }}</option>
                            @endforeach
                        @endif
                    @endslot
                @endcomponent
            </div>
            @if (in_array(Auth::user()->role, ['Admin', 'Sub Admin', 'Institusi']))
                @if (in_array(Auth::user()->role, ['Admin', 'Sub Admin']))
                    <div class="col-lg">
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
            @if (Auth::user()->role == 'Surveyor')
                <div class="col-lg-4">
                    @component('components.formGroup.select', [
                        'label' => 'Pilih Status',
                        'name' => 'status',
                        'id' => 'status',
                        'class' => 'select2 filter',
                    ])
                        @slot('options')
                            <option value="semua">Semua</option>
                            <option value="selesai">Selesai</option>
                            <option value="belum_selesai">Belum Selesai</option>
                        @endslot
                    @endcomponent
                </div>
            @endif
            <div class="col-lg-4 col-md-4">
                {{-- Kabupaten / Kota --}}
                @component('components.formGroup.select', [
                    'label' => 'Kabupaten / Kota',
                    'name' => 'kabupaten_kota',
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
                    'name' => 'kecamatan',
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
                    'name' => 'desa_kelurahan',
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
                    'name' => 'lokasi_survey',
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
        </div>
    </section>

    @if (Auth::user()->role == 'Surveyor')
        <section class="mt-3">
            <div class="row mb-3">
                <div class="col">
                    @component('components.buttons.add')
                        @slot('href')
                            {{ url('/survey/pilih-responden') }}
                        @endslot
                        @slot('onClick')
                        @endslot
                    @endcomponent
                </div>
            </div>
        </section>
    @endif


    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered yajra-datatable">
                <thead>
                    <tr class="text-center  ">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Supervisor / DPL</th>
                        <th>Lokasi Survey</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            getKabupaten();
        });

        $('.date').keydown(function(e) {
            e.preventDefault();
        });


        var idInstitusi = "{{ Auth::user()->profile->institusi_id }}";
        getSupervisor(idInstitusi);

        function hapus(id) {
            swal({
                title: "Apakah anda yakin?",
                text: "Soal ini akan dihapus secara permanen",
                icon: "warning",
                buttons: {
                    confirm: {
                        text: "Ya",
                        className: 'btn btn-success'
                    },
                    cancel: {
                        visible: true,
                        text: "Tidak",
                        className: 'btn btn-danger'
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ url('/survey/lihat-survey/') . '/' }}" + id,
                        type: "DELETE",
                        success: function(response) {
                            if (response.status == "success") {
                                swal("Berhasil",
                                    "Survey berhasil dihapus", {
                                        icon: "success",
                                        buttons: {
                                            confirm: {
                                                className: 'btn btn-success'
                                            }
                                        },
                                    });
                                table.draw();
                            } else {
                                swal("Gagal",
                                    "Survey gagal dihapus", {
                                        icon: "error",
                                        buttons: {
                                            confirm: {
                                                className: 'btn btn-danger'
                                            }
                                        },
                                    });
                            }
                        },
                        error: function(response) {
                            swal("Gagal",
                                "Survey gagal dihapus", {
                                    icon: "error",
                                    buttons: {
                                        confirm: {
                                            className: 'btn btn-danger'
                                        }
                                    },
                                });
                        }
                    });
                }
            });
        }

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ url('/survey/daftar-survey') }}",
                data: function(d) {
                    d.nama_survey_id = $('#nama_survey_id').val();
                    d.institusi_id = $('#institusi_id').val();
                    d.supervisor_id = $('#supervisor_id').val();
                    d.kabupaten_kota_id = $('#kabupaten-kota').val();
                    d.kecamatan_id = $('#kecamatan').val();
                    d.desa_kelurahan_id = $('#desa-kelurahan').val();
                    d.lokasi_survey_id = $('#lokasi-survey').val();
                    d.tanggal_sampai = $('#tanggal_sampai').val();
                    d.tanggal_dari = $('#tanggal_dari').val();
                    d.status = $('#status').val();
                    d.search = $('input[type="search"]').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'supervisor',
                    name: 'supervisor'
                },
                {
                    data: 'lokasi_survey',
                    name: 'lokasi_survey'
                },
                {
                    data: 'tipe',
                    name: 'tipe',
                    className: 'text-center',
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center',
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    className: 'text-center',
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    orderable: true,
                    searchable: true
                },
            ],
        });

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
                });
            }
        });

        $('.filter').change(function() {
            table.draw();
        })

        $('.tanggal').keyup(function() {
            table.draw();
        })
    </script>
@endpush
