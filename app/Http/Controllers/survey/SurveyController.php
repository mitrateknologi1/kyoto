<?php

namespace App\Http\Controllers\survey;

use App\Http\Controllers\Controller;
use App\Models\JawabanSurvey;
use App\Models\KategoriSoal;
use App\Models\NamaSurvey;
use App\Models\Responden;
use App\Models\Soal;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Survey::with(['responden', 'namaSurvey', 'profile'])->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return '<h6 class="text-uppercase mb-1 mt-4">Surveyor: ' . $row->profile->nama_lengkap . '</h6>
                                    <h6 class="text-uppercase fw-bold mb-0">Responden: ' . $row->responden->kartu_keluarga . '</h6>
                                    <span class="text-muted mb-4">Judul:  ' . $row->namaSurvey->nama . '</span>';
                })
                ->addColumn('tipe', function ($row) {
                    if ($row->namaSurvey->tipe == "Pre") {
                        return '<span class="text-warning">PRE</span>';
                    } else {
                        return '<span class="text-danger">POST</span>';
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
                            <a href="' . url('/survey/pertanyaan-survey') . "/" . $row->id . "/" . $row->kategori_selanjutnya . '" class="btn btn-warning btn-sm mr-1 my-1" title="Ubah"><i class="far fa-play-circle"></i> Lanjutkan</a>
                        </div>';
                    } else {
                        $kategori = KategoriSoal::where('nama_survey_id', $row->nama_survey_id)->orderBy('id', 'asc')->first();
                        $actionBtn = '
                            <a href="' . url('/survey/lihat-survey') . "/" . $row->id . '" class="btn btn-primary btn-sm mr-1 my-1" title="Ubah" target="_blank"><i class="fas fa-eye"></i> Lihat</a>
                             <a href="' . url('/survey/pertanyaan-survey') . "/" . $row->id . "/" . $kategori->id . '" class="btn btn-warning btn-sm mr-1 my-1" title="Ubah" ><i class="fas fa-edit"></i> Ubah</a>
                             <button id="btn-delete" onclick="hapus(' . $row->id . ')" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                        </div>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['action', 'nama', 'tipe', 'status'])
                ->make(true);
        }
        return view('pages.survey.index');
    }

    public function pilihResponden()
    {
        $responden = Responden::orderBy('id', 'desc')->get();
        $namaSurvey = NamaSurvey::orderBy('id', 'desc')->get();
        return view('pages.survey.pilihResponden', compact('responden', 'namaSurvey'));
    }

    public function cekPilihResponden(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'responden_id' => ['required', Rule::unique('survey')->where(function ($query) use ($request) {
                    $query->where([
                        ['responden_id', $request->responden_id],
                        ['nama_survey_id', $request->nama_survey_id]
                    ]);
                })],
                'nama_survey_id' => ['required', Rule::unique('survey')->where(function ($query) use ($request) {
                    $query->where([
                        ['responden_id', $request->responden_id],
                        ['nama_survey_id', $request->nama_survey_id]
                    ]);
                })],
            ],
            [
                'responden_id.required' => "Responden Tidak Boleh Dikosongkan",
                'nama_survey_id.required' => "Nama Survey Tidak Boleh Dikosongkan",
                'nama_survey_id.unique' => "Survey Sudah Ada",
                'responden_id.unique' => "Survey Sudah Ada",
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
        $survey->responden_id = $request->responden_id;
        $survey->nama_survey_id = $request->nama_survey_id;
        $survey->kategori_selanjutnya = $kategoriAwal;
        $survey->profile_id = auth()->user()->profile->id;
        $survey->save();

        $kategori = KategoriSoal::where('nama_survey_id', $request->nama_survey_id)->orderBy('id', 'asc')->first();

        return response()->json([
            'status' => 'success',
            'id_survey' => $survey->id,
            'id_kategori' => $kategori->id
        ]);
    }

    public function pertanyaanSurvey($survey, $kategori, Request $request)
    {
        $idSurvey = $survey;
        $idKategori = $kategori;

        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('id', $idSurvey)->first();

        $semuaKategori = KategoriSoal::where('nama_survey_id', $survey->nama_survey_id)->get();
        $kategori = KategoriSoal::with(['soal'])->where('nama_survey_id', $survey->nama_survey_id)->where('id', $idKategori)->orderBy('urutan', 'asc')->get();
        $indexKategori = array_search($idKategori, $semuaKategori->pluck('id')->toArray());

        $jawabanSurvey = JawabanSurvey::with(['jawabanSoal'])->where('survey_id', $idSurvey)->where('kategori_soal_id', $idKategori)->get();

        if (($indexKategori + 1) == count($semuaKategori)) {
            $tombolSelanjutnya = 'Simpan';
        } else {
            $tombolSelanjutnya = 'Selanjutnya';
        }

        if (($indexKategori - 1) < 0) {
            $tombolSebelumnya = '';
            $urlSebelumnya = '';
        } else {
            $tombolSebelumnya = 'Sebelumnya';
            $urlSebelumnya = url('/survey/pertanyaan-survey/') . "/" . $idSurvey . "/" . $semuaKategori[$indexKategori - 1]->id;
        }


        $kategori = $kategori[0];
        return view('pages.survey.pertanyaanSurvey', compact('kategori', 'tombolSelanjutnya', 'tombolSebelumnya', 'urlSebelumnya', 'idSurvey', 'jawabanSurvey'));
    }

    public function cekJawabanSurvey($survey, Request $request)
    {
        $survey_id = $survey; //Ganti Nanti
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

        $jawabanSurvey = JawabanSurvey::where('survey_id', $survey_id)->where('kategori_soal_id', $kategori_soal_id)->get();
        if ($jawabanSurvey->count() > 0) {
            $jawabanSurvey = JawabanSurvey::where('survey_id', $survey_id)->where('kategori_soal_id', $kategori_soal_id)->delete();
        }

        for ($i = 0; $i < count($request->id); $i++) {
            $jawabanSurvey = new JawabanSurvey();
            $jawaban = "jawaban-" . ($i + 1);
            $jawabanLainnya = "jawaban-lainnya-" . ($i + 1);
            if ($request->tipe_jawaban[$i] == 'Jawaban Singkat') {
                $jawabanSurvey->soal_id = $request->id[$i];
                $jawabanSurvey->survey_id = $survey_id;
                $jawabanSurvey->jawaban_lainnya = $request->$jawaban;
                $jawabanSurvey->kategori_soal_id = $kategori_soal_id;
                $jawabanSurvey->save();
            } else {
                for ($j = 0; $j < count($request->$jawaban); $j++) {
                    $jawabanSurvey = new JawabanSurvey();
                    $jawabanSurvey->soal_id = $request->id[$i];
                    $jawabanSurvey->survey_id = $survey_id;
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

        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('id', $survey_id)->first();

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
            $url = url('/survey/pertanyaan-survey/') . "/" . $survey_id . "/" . $kategori[$indexKategori + 1]->id;
        }

        return response()->json(
            [
                'status' => 'success',
                'url' => $url
            ]
        );
    }

    public function lihatSurvey($id)
    {
        $idSurvey = $id;
        $survey = Survey::with(['responden', 'namaSurvey', 'profile'])->where('id', $id)->first();
        $daftarKategori = KategoriSoal::with(['soal', 'jawabanSurvey' => function ($jawabanSurvey) use ($idSurvey) {
            $jawabanSurvey->where('survey_id', $idSurvey);
        }])->where('nama_survey_id', $survey->nama_survey_id)->orderBy('urutan', 'asc')->get();

        return view('pages.survey.lihatSurvey', compact('survey', 'daftarKategori', 'idSurvey'));
    }

    public function delete($id)
    {
        $survey = Survey::find($id);
        $survey->delete();

        $jawabanSurvey = JawabanSurvey::where('survey_id', $id)->get();
        if ($jawabanSurvey->count() > 0) {
            $jawabanSurvey = JawabanSurvey::where('survey_id', $id)->delete();
        }
        return response()->json(['status' => 'success']);
    }
}
