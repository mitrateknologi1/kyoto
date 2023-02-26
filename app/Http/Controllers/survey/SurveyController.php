<?php

namespace App\Http\Controllers\survey;

use App\Http\Controllers\Controller;
use App\Models\Institusi;
use App\Models\JawabanSurvey;
use App\Models\KategoriSoal;
use App\Models\NamaSurvey;
use App\Models\Responden;
use App\Models\Soal;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $namaSurveyId = $request->nama_survey_id;
        $institusiId = $request->institusi_id;
        $supervisorId = $request->supervisor_id;
        $status = $request->status;
        $search = $request->search;

        $namaSurvey = NamaSurvey::all();
        if ($request->ajax()) {
            $data = Survey::with(['responden', 'namaSurvey', 'profile'])
                ->whereHas('namaSurvey', function ($namaSurvey) use ($namaSurveyId) {
                    if ($namaSurveyId != 'semua' && $namaSurveyId != null) {
                        $namaSurvey->where('nama_survey_id', $namaSurveyId);
                    }
                })
                ->whereHas('profile', function ($query) use ($institusiId) {
                    if (in_array(Auth::user()->role, ["Admin", 'Sub Admin'])) {
                        if ($institusiId != 'semua' && $institusiId != null) {
                            $query->where('institusi_id', $institusiId);
                        }
                    } else if (Auth::user()->role == "Institusi") {
                        $query->where('institusi_id', Auth::user()->profile->institusi_id);
                    }
                })
                ->where(function ($query) use ($supervisorId) {
                    if (in_array(Auth::user()->role, ["Admin", 'Institusi', 'Sub Admin'])) {
                        if ($supervisorId != 'semua' && $supervisorId != null) {
                            $query->whereHas('supervisor', function ($query) use ($supervisorId) {
                                $query->where('profile_dpl', $supervisorId);
                            });
                        }
                    }
                })
                ->where(function ($query) {
                    if (Auth::user()->role == "Surveyor") {
                        $query->where('profile_id', Auth::user()->profile->id);
                    }
                })->where(function ($query) use ($status) {
                    if (Auth::user()->role == "Surveyor") {
                        if ($status != 'semua' && $status != null) {
                            if ($status == 'selesai') {
                                $query->where('is_selesai', 1);
                            } else {
                                $query->where('is_selesai', 0);
                            }
                        }
                    } else {
                        $query->where('is_selesai', 1);
                    }
                })->where(function ($query) use ($search) {
                    if ($search) {
                        $query->whereHas('responden', function ($query) use ($search) {
                            $query->where('kartu_keluarga', 'like', '%' . $search . '%');
                            $query->orWhere('nama_kepala_keluarga', 'like', '%' . $search . '%');
                        });

                        $query->orWhereHas('namaSurvey', function ($query) use ($search) {
                            $query->where('nama', 'like', '%' . $search . '%');
                        });

                        $query->orWhereHas('profile', function ($query) use ($search) {
                            $query->where('nama_lengkap', 'like', '%' . $search . '%');
                        });
                    }
                })->orderBy('updated_at', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    if (in_array(Auth::user()->role, ['Admin', 'Institusi', 'Sub Admin'])) {
                        return '<h6 class="text-uppercase mb-1 mt-4">Surveyor: ' . $row->profile->nama_lengkap . '</h6>
                                    <h6 class="text-uppercase fw-bold mb-0">Responden: ' . $row->responden->kartu_keluarga . ' (' . $row->responden->nama_kepala_keluarga . ')</h6>
                                    <span class="text-muted mb-4">Judul:  ' . $row->namaSurvey->nama . '</span>
                                    <br>
                                    <span class="text-muted mb-4">Institusi / Universitas:  ' . $row->profile->institusi->nama . '</span>';
                    } else if (Auth::user()->role == "Surveyor") {
                        return '<h6 class="text-uppercase fw-bold mb-0">Responden: ' . $row->responden->kartu_keluarga . ' (' . $row->responden->nama_kepala_keluarga . ')</h6>
                                    <span class="text-muted mb-4">Judul:  ' . $row->namaSurvey->nama . '</span>';
                    }
                })
                ->addColumn('supervisor', function ($row) {
                    if (count($row->supervisor)) {
                        $daftarSupervisor = '';
                        foreach ($row->supervisor as $supervisor) {
                            $daftarSupervisor .= '<p> - ' . $supervisor->profileSupervisor->nama_lengkap . '</p>';
                        }
                    } else {
                        $daftarSupervisor = '-';
                    }
                    return $daftarSupervisor;
                })
                ->addColumn('lokasi_survey', function ($row) {
                    $lokasiSurvey = '';
                    if ($row->profile->anggotaSupervisor) {
                        $lokasiSurvey .= '<p class="mb-0 pb-0">' . $row->profile->anggotaSupervisor->lokasiSurveySupervisor->lokasiSurvey->nama_lokasi_survey . '</p>
                                            <h6 class="fw-bold">' . $row->profile->anggotaSupervisor->lokasiSurveySupervisor->lokasiSurvey->desa_kelurahan->nama . '</h6>';
                    } else {
                        $lokasiSurvey .= '-';
                    }
                    return $lokasiSurvey;
                })
                ->addColumn('tipe', function ($row) {
                    if ($row->namaSurvey->tipe == "Pre") {
                        return '<span class="badge badge-primary">PRE</span>';
                    } else {
                        return '<span class="badge badge-success">POST</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    if ($row->is_selesai == "0") {
                        return '<span class="badge badge-warning">Belum Selesai</span>';
                    } else {
                        return '<span class="badge badge-success">Selesai</span>';
                    }
                })
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('d F Y');
                })
                ->addColumn('action', function ($row) {
                    if ($row->is_selesai == "0") {
                        $actionBtn = '
                            <a href="' . url('/survey/pertanyaan-survey') . "/" . $row->kode_unik . "/" . $row->kategori_selanjutnya . '" class="btn btn-warning btn-sm mr-1 my-1" title="Ubah"><i class="far fa-play-circle"></i> Lanjutkan</a>
                        </div>';
                    } else {
                        $kategori = KategoriSoal::where('nama_survey_id', $row->nama_survey_id)->orderBy('id', 'asc')->first();

                        $actionBtn = '<a href="' . url('/survey/lihat-survey') . "/" . $row->kode_unik . '" class="btn btn-primary btn-sm mr-1 my-1" title="Ubah" target="_blank"><i class="fas fa-eye"></i> Lihat</a>';

                        if (Auth::user()->role == "Surveyor") {
                            $actionBtn .= '
                             <a href="' . url('/survey/pertanyaan-survey') . "/" . $row->kode_unik . "/" . $kategori->id . '" class="btn btn-warning btn-sm mr-1 my-1" title="Ubah" ><i class="fas fa-edit"></i> Ubah</a>';
                        }

                        if (!in_array(Auth::user()->role, ["Institusi", 'Sub Admin'])) {
                            $actionBtn .= '<button id="btn-delete" onclick="hapus(' . $row->kode_unik . ')" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->kode_unik . '" title="Hapus"><i class="fas fa-trash"></i> Hapus</button></div>';
                        }
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'nama', 'supervisor', 'tipe', 'status', 'lokasi_survey'])
                ->make(true);
        }
        $institusi = Institusi::orderBy('nama', 'asc')->get();
        return view('pages.survey.index', compact('namaSurvey', 'institusi'));
    }

    public function pilihResponden()
    {
        $startDate = now()->subMonths(3);
        $endDate = now();

        $responden = Responden::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc')->get();
        $namaSurvey = NamaSurvey::orderBy('id', 'desc')->where('is_aktif', 1)
            ->whereHas('kategoriSoal', function ($query) {
                $query->whereHas('soal');
            })
            ->get();
        return view('pages.survey.pilihResponden', compact('responden', 'namaSurvey'));
    }

    public function cekPilihResponden(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'kode_unik_responden' => ['required', Rule::unique('survey')->where(function ($query) use ($request) {
                    $query->where([
                        ['kode_unik_responden', $request->kode_unik_responden],
                        ['nama_survey_id', $request->nama_survey_id]
                    ]);
                })],
                'nama_survey_id' => ['required', Rule::unique('survey')->where(function ($query) use ($request) {
                    $query->where([
                        ['kode_unik_responden', $request->kode_unik_responden],
                        ['nama_survey_id', $request->nama_survey_id]
                    ]);
                })],
            ],
            [
                'kode_unik_responden.required' => "Responden Tidak Boleh Dikosongkan",
                'nama_survey_id.required' => "Nama Survey Tidak Boleh Dikosongkan",
                'nama_survey_id.unique' => "Survey Sudah Ada",
                'kode_unik_responden.unique' => "Survey Sudah Ada",
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $namaSurvey = NamaSurvey::find($request->nama_survey_id);
        $kategoriAwal = $namaSurvey->kategoriSoal[0]->id;
        if ($namaSurvey->kategoriSoal->count() == 0) {
            return response()->json([
                'status' => 'error',
                'pesan' => 'Survey Belum Memiliki Kategori Soal'
            ]);
        } else {
            foreach ($namaSurvey->kategoriSoal as $kategoriSoal) {
                if ($kategoriSoal->soal->count() == 0) {
                    return response()->json([
                        'status' => 'error',
                        'pesan' => 'Pastikan Setiap Kategori Memiliki Minimal 1 Soal'
                    ]);
                }
            }
        }

        $survey = new Survey();
        $survey->kode_unik_responden = $request->kode_unik_responden;
        $survey->nama_survey_id = $request->nama_survey_id;
        $survey->kategori_selanjutnya = $kategoriAwal;
        $survey->profile_id = auth()->user()->profile->id;
        $survey->kode_unik = $this->generateKodeUnik();
        $survey->save();

        $kategori = KategoriSoal::where('nama_survey_id', $request->nama_survey_id)->orderBy('id', 'asc')->first();
        $url = url('/survey/pertanyaan-survey' . "/" . $survey->kode_unik . "/" . $kategori->id);

        return response()->json([
            'status' => 'success',
            'url' => $url
        ]);
    }

    public function pertanyaanSurvey($survey, $kategori, Request $request)
    {
        $kodeUnik = $survey;
        $idKategori = $kategori;

        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('kode_unik', $kodeUnik)->first();

        if ($survey->profile_id != auth()->user()->profile->id) {
            return redirect('/survey/daftar-survey');
        }

        $semuaKategori = KategoriSoal::where('nama_survey_id', $survey->nama_survey_id)->get();
        $kategori = KategoriSoal::with(['soal'])->where('nama_survey_id', $survey->nama_survey_id)->where('id', $idKategori)->orderBy('urutan', 'asc')->get();
        $indexKategori = array_search($idKategori, $semuaKategori->pluck('id')->toArray());

        if (($indexKategori + 1) == count($semuaKategori)) {
            $tombolSelanjutnya = 'Selanjutnya';
        } else {
            $tombolSelanjutnya = 'Selanjutnya';
        }

        if (($indexKategori - 1) < 0) {
            $tombolSebelumnya = '';
            $urlSebelumnya = '';
        } else {
            $tombolSebelumnya = 'Sebelumnya';
            $urlSebelumnya = url('/survey/pertanyaan-survey/') . "/" . $kodeUnik . "/" . $semuaKategori[$indexKategori - 1]->id;
        }


        $kategori = $kategori[0];
        return view('pages.survey.pertanyaanSurvey', compact('kategori', 'tombolSelanjutnya', 'tombolSebelumnya', 'urlSebelumnya', 'kodeUnik', 'survey'));
    }

    public function cekJawabanSurvey($survey, Request $request)
    {
        $kodeUnik = $survey;
        $kategori_soal_id = $request->kategori_soal_id;

        // Validasi Data
        $pesanError = [];
        for ($i = 0; $i < count($request->id); $i++) {
            $jawaban = "jawaban-" . ($i + 1);
            $jawabanLainnya = "jawaban-lainnya-" . ($i + 1);
            if ($request->tipe_jawaban[$i] == 'Jawaban Singkat') {
                if ($request->$jawaban == null) {
                    $pesanError['jawaban-' . ($i + 1)] = 'Jawaban Tidak Boleh Kosong';
                }
            } else {
                if ($request->$jawaban == null) {
                    $pesanError['jawaban-' . ($i + 1)] = 'Jawaban Tidak Boleh Kosong';
                } else {
                    for ($j = 0; $j < count($request->$jawaban); $j++) {
                        if ($request->$jawaban[$j] == 'Lainnya') {
                            if ($request->$jawabanLainnya == null) {
                                $pesanError['jawaban-' . ($i + 1)] = 'Jawaban Lainnya Tidak Boleh Kosong';
                            }
                        }
                    }
                }
            }
        }

        if (!empty($pesanError)) {
            return response()->json([
                'error' => $pesanError
            ]);
        }

        $jawabanSurvey = JawabanSurvey::where('kode_unik_survey', $kodeUnik)->where('kategori_soal_id', $kategori_soal_id)->get();
        if ($jawabanSurvey->count() > 0) {
            $jawabanSurvey = JawabanSurvey::where('kode_unik_survey', $kodeUnik)->where('kategori_soal_id', $kategori_soal_id)->delete();
        }

        for ($i = 0; $i < count($request->id); $i++) {
            $jawabanSurvey = new JawabanSurvey();
            $jawaban = "jawaban-" . ($i + 1);
            $jawabanLainnya = "jawaban-lainnya-" . ($i + 1);
            if ($request->tipe_jawaban[$i] == 'Jawaban Singkat') {
                $jawabanSurvey->soal_id = $request->id[$i];
                $jawabanSurvey->kode_unik_survey = $kodeUnik;
                $jawabanSurvey->jawaban_lainnya = $request->$jawaban;
                $jawabanSurvey->kategori_soal_id = $kategori_soal_id;
                $jawabanSurvey->save();
            } else {
                for ($j = 0; $j < count($request->$jawaban); $j++) {
                    $jawabanSurvey = new JawabanSurvey();
                    $jawabanSurvey->soal_id = $request->id[$i];
                    $jawabanSurvey->kode_unik_survey = $kodeUnik;
                    if ($request->$jawaban[$j] == 'Lainnya') {
                        $jawabanSurvey->jawaban_lainnya = $request->$jawabanLainnya;
                    } else {
                        $jawabanSurvey->jawaban_soal_id = $request->$jawaban[$j];
                    }
                    $jawabanSurvey->kategori_soal_id = $kategori_soal_id;
                    $jawabanSurvey->save();
                }
            }
        }

        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('kode_unik', $kodeUnik)->first();

        $kategori = KategoriSoal::with(['soal'])->where('nama_survey_id', $survey->nama_survey_id)->orderBy('urutan', 'asc')->get();
        $indexKategori = array_search($kategori_soal_id, $kategori->pluck('id')->toArray());
        if (($indexKategori + 1) == count($kategori)) {
            $survey->is_selesai = 1;
            $survey->kategori_selanjutnya = null;
            $url = url('/survey/daftar-survey/');
            $survey->save();
        } else {
            if ($survey->is_selesai != 1) {
                $survey->kategori_selanjutnya = $kategori[$indexKategori + 1]->id;
            }
            $survey->save();
            $url = url('/survey/pertanyaan-survey/') . "/" . $kodeUnik . "/" . $kategori[$indexKategori + 1]->id;
            // touch survey to update field updatedAt into current time stamp
            $survey->touch();
        }

        return response()->json(
            [
                'status' => 'success',
                'url' => $url
            ]
        );
    }

    public function lihatSurvey($kodeUnik)
    {
        $kodeUnik = $kodeUnik;
        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('kode_unik', $kodeUnik)->first();
        if (Auth::user()->role == "Surveyor") {
            if ($survey->profile_id != auth()->user()->profile->id) {
                return redirect('/survey/daftar-survey');
            }
        }

        $daftarKategori = KategoriSoal::with(['soal'])->where('nama_survey_id', $survey->nama_survey_id)->get();

        return view('pages.survey.lihatSurvey', compact('survey', 'daftarKategori', 'kodeUnik'));
    }

    public function delete($kodeUnik)
    {
        $survey = Survey::where('kode_unik', $kodeUnik)->first();
        $survey->delete();

        $jawabanSurvey = JawabanSurvey::where('kode_unik_survey', $kodeUnik)->get();
        if ($jawabanSurvey->count() > 0) {
            $jawabanSurvey = JawabanSurvey::where('kode_unik_survey', $kodeUnik)->delete();
        }
        return response()->json(['status' => 'success']);
    }

    //
    public function generateKodeUnik()
    {
        do {
            $code = random_int(10000000, 99999999);
        } while (Survey::where("kode_unik", "=", $code)->first());

        return $code;
    }
}
