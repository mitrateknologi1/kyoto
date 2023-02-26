@extends('templates/dashboard')

@section('title-tab')
    Dashboard
@endsection

@section('title')
    Dashboard
@endsection

@section('subTitle')
    Dashboard
@endsection

@push('style')
@endpush

@section('content')
    <section>
        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-chart-pie text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Data Survey</p>
                                            <h4 class="card-title">{{ $totalSurvey }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach ($nama_survey as $item)
                        <div class="col-12">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="icon-big text-center">
                                                <i class="fas fa-tasks text-success"></i>
                                            </div>
                                        </div>
                                        <div class="col-8 col-stats">
                                            <div class="numbers">
                                                <p class="card-category">{{ $item->nama }}</p>
                                                <h4 class="card-title">
                                                    {{ $survey->where('nama_survey_id', $item->id)->count() }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-users text-secondary"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Surveyor / Mahasiswa</p>
                                            <h4 class="card-title">{{ $totalSurveyor }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-users text-danger"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Supervisor / DPL</p>
                                            <h4 class="card-title">{{ $totalSupervisor }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-stats card-round">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="icon-big text-center">
                                            <i class="fas fa-map-marked-alt text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 col-stats">
                                        <div class="numbers">
                                            <p class="card-category">Lokasi Survey</p>
                                            <h4 class="card-title">{{ $totalLokasiSurvey }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Riwayat Survey</div>
                            <div class="card-tools">
                                <ul class="nav nav-pills nav-secondary nav-pills-no-bd nav-sm" id="pills-tab"
                                    role="tablist">
                                    <li class="nav-item" style="float: right">
                                        <a href="#v-pills-today" class="nav-link active show" id="pills-today"
                                            data-toggle="pill" href="#pills-today" role="tab" aria-selected="true">Hari
                                            Ini
                                            ({{ $riwayatSurveyHariIni->count() }}) </a>
                                    </li>
                                    <li class="nav-item" style="float: right">
                                        <a href="#v-pills-weeks" class="nav-link" id="pills-week" data-toggle="pill"
                                            href="#pills-week" role="tab" aria-selected="false">Seminggu Terakhir
                                            ({{ $riwayatSurveyMingguIni->count() }})</a>
                                    </li>
                                    <li class="nav-item" style="float: right">
                                        <a href="#v-pills-month" class="nav-link" id="pills-month" data-toggle="pill"
                                            href="#pills-month" role="tab" aria-selected="false">Sebulan Terakhir
                                            ({{ $riwayatSurveyBulanIni->count() }})</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="height: 430px; overflow: auto">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade active show" id="v-pills-today" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                @forelse ($riwayatSurveyHariIni->get() as $item)
                                    <div class="d-flex">
                                        <div class="flex-1 ml-3 pt-1">
                                            <h6 class="text-uppercase mb-1">Surveyor: {{ $item->profile->nama_lengkap }}
                                            </h6>
                                            <h6 class="text-uppercase fw-bold mb-0">Responden:
                                                {{ $item->responden->kartu_keluarga }}
                                                @if ($item->namaSurvey->tipe == 'Pre')
                                                    <span class="badge badge-danger ml-3">Pre</span>
                                                @else
                                                    <span class="badge badge-success ml-3">Post</span>
                                                @endif
                                                <span class="text-muted d-block">Judul: {{ $item->namaSurvey->nama }}
                                                </span>
                                        </div>
                                        <div class="float-right pt-1">
                                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                            <div>
                                                <a href="{{ url('/survey/lihat-survey/') }}/{{ $item->kode_unik }}"
                                                    class="badge badge-primary mt-1 float-right text-white"
                                                    target="_blank">Lihat</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator-dashed"></div>
                                @empty
                                    <h4 class="text-muted text-center mt-2">Belum ada data</h4>
                                @endforelse

                            </div>
                            <div class="tab-pane fade" id="v-pills-weeks" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                @forelse ($riwayatSurveyMingguIni->get() as $item)
                                    <div class="d-flex">
                                        <div class="flex-1 ml-3 pt-1">
                                            <h6 class="text-uppercase mb-1">Surveyor: {{ $item->profile->nama_lengkap }}
                                            </h6>
                                            <h6 class="text-uppercase fw-bold mb-0">Responden:
                                                {{ $item->responden->kartu_keluarga }}
                                                @if ($item->namaSurvey->tipe == 'Pre')
                                                    <span class="badge badge-danger ml-3">Pre</span>
                                                @else
                                                    <span class="badge badge-success ml-3">Post</span>
                                                @endif
                                                <span class="text-muted d-block">Judul: {{ $item->namaSurvey->nama }}
                                                </span>
                                        </div>
                                        <div class="float-right pt-1">
                                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                            <div>
                                                <a href="{{ url('/survey/lihat-survey/') }}/{{ $item->kode_unik }}"
                                                    class="badge badge-primary mt-1 float-right text-white"
                                                    target="_blank">Lihat</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator-dashed"></div>
                                @empty
                                    <h3>Tidak ada data</h3>
                                @endforelse
                            </div>
                            <div class="tab-pane fade" id="v-pills-month" role="tabpanel"
                                aria-labelledby="v-pills-home-tab">
                                @forelse ($riwayatSurveyBulanIni->get() as $item)
                                    <div class="d-flex">
                                        <div class="flex-1 ml-3 pt-1">
                                            <h6 class="text-uppercase mb-1">Surveyor: {{ $item->profile->nama_lengkap }}
                                            </h6>
                                            <h6 class="text-uppercase fw-bold mb-0">Responden:
                                                {{ $item->responden->kartu_keluarga }}
                                                @if ($item->namaSurvey->tipe == 'Pre')
                                                    <span class="badge badge-danger ml-3">Pre</span>
                                                @else
                                                    <span class="badge badge-success ml-3">Post</span>
                                                @endif
                                                <span class="text-muted d-block">Judul: {{ $item->namaSurvey->nama }}
                                                </span>
                                        </div>
                                        <div class="float-right pt-1">
                                            <small class="text-muted">{{ $item->created_at->format('d/m/Y') }}</small>
                                            <div>
                                                <a href="{{ url('/survey/lihat-survey/') }}/{{ $item->kode_unik }}"
                                                    class="badge badge-primary mt-1 float-right text-white"
                                                    target="_blank">Lihat</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator-dashed"></div>
                                @empty
                                    <h3>Tidak ada data</h3>
                                @endforelse
                            </div>

                        </div>


                    </div>
                </div>
            </div>

        </div>
    </section>
    <section>
        <div class="row">
        </div>
    </section>
@endsection

@push('script')
    <script>
        $(function() {
            statistik()
            $('#tahun').change(function() {
                statistik()
                // location.reload()
            })
        })

        function statistik() {
            var tahun = $('#tahun').val();
            $.ajax({
                url: "{{ route('dashboardStatistik') }}",
                type: "GET",
                data: {
                    tahun: tahun
                },
                dataType: "json",
                success: function(data) {
                    console.log(data)
                    var pre = data.jsonJumlahPerbulanPre
                    var post = data.jsonJumlahPerbulanPost
                    var bulan = data.jsonNamaBulan
                    chart(pre, post, bulan)
                }
            })
        }
        var chart;

        function chart(pre, post, bulan) {
            if (chart) {
                chart.destroy();
            }

            var ctx = document.getElementById('statisticsChart').getContext('2d');

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: bulan,
                    datasets: [{
                            label: "Post",
                            borderColor: '#34CE39',
                            pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
                            pointRadius: 0,
                            backgroundColor: 'rgba(52, 206, 57, 0.5)',
                            legendColor: '#34CE39',
                            fill: true,
                            borderWidth: 2,
                            data: post
                        },
                        {
                            label: "Pre",
                            borderColor: '#fdaf4b',
                            pointBackgroundColor: 'rgba(253, 175, 75, 0.6)',
                            pointRadius: 0,
                            backgroundColor: 'rgba(255, 173, 70, 0.5)',
                            legendColor: '#fdaf4b',
                            fill: true,
                            borderWidth: 2,
                            data: pre
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        bodySpacing: 4,
                        mode: "nearest",
                        intersect: 0,
                        position: "nearest",
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    layout: {
                        padding: {
                            left: 5,
                            right: 5,
                            top: 15,
                            bottom: 15
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                fontStyle: "500",
                                beginAtZero: false,
                                maxTicksLimit: 5,
                                padding: 10
                            },
                            gridLines: {
                                drawTicks: false,
                                display: false
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                zeroLineColor: "transparent"
                            },
                            ticks: {
                                padding: 10,
                                fontStyle: "500"
                            }
                        }]
                    },
                    // legendCallback: function(chart) { 
                    //     // clear 
                    //     // $('#statisticsChart').html('')
                    //     $('#statisticsChart').remove();


                    //     var text = []; 
                    //     text.push('<ul class="' + chart.id + '-legend html-legend">'); 
                    //     for (var i = 0; i < chart.data.datasets.length; i++) { 
                    //         text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>'); 
                    //         if (chart.data.datasets[i].label) { 
                    //             text.push(chart.data.datasets[i].label); 
                    //         } 
                    //         text.push('</li>'); 
                    //     } 
                    //     text.push('</ul>'); 
                    //     return text.join(''); 
                    // }  
                }
            });
        }
    </script>
@endpush
