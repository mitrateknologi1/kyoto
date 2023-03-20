<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kecamatan;
use App\Models\Responden;
use Illuminate\Http\Request;
use App\Models\Desa_kelurahan;
use App\Models\Kabupaten_kota;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\LokasiSurvey;
use App\Models\Profile;

class ListController extends Controller
{
    public function listProvinsi(Request $request)
    {
        if ($request->is_responden == 1) {
            $url = Provinsi::where('status', 1)->orderBy('nama', 'ASC')->get();
        } else {
            $url = Provinsi::orderBy('nama', 'ASC')->get();
        }
        $json = json_decode(($url));
        return $json;
    }

    public function listKabupatenKota(Request $request)
    {
        if ($request->is_responden == 1) {
            $url = Kabupaten_kota::where('provinsi_id', $request->idProvinsi)->where('status', 1)->orderBy('nama', 'ASC')->get();
        } else {
            $url = Kabupaten_kota::where('provinsi_id', $request->idProvinsi)->orderBy('nama', 'ASC')->get();
        }
        $json = json_decode(($url));
        return $json;
    }

    public function listKecamatan(Request $request)
    {
        if ($request->is_responden == 1) {
            $url = Kecamatan::where('kabupaten_kota_id', $request->idKabupatenKota)->where('status', 1)->orderBy('nama', 'ASC')->get();
        } else {
            $url = Kecamatan::where('kabupaten_kota_id', $request->idKabupatenKota)->orderBy('nama', 'ASC')->get();
        }
        $json = json_decode(($url));
        return $json;
    }

    public function listDesaKelurahan(Request $request)
    {
        if ($request->is_responden == 1) {
            $url = Desa_kelurahan::where('kecamatan_id', $request->idKecamatan)->where('status', 1)->orderBy('nama', 'ASC')->get();
        } else {
            $url = Desa_kelurahan::where('kecamatan_id', $request->idKecamatan)->orderBy('nama', 'ASC')->get();
        }
        $json = json_decode(($url));
        return $json;
    }

    public function listLokasiSurvey(Request $request)
    {
        $idLokasiSurvey = $request->idLokasiSurvey;
        $idDesaKelurahan = $request->idDesaKelurahan;
        $lokasiSurvey = LokasiSurvey::with(['desa_kelurahan', 'desa_kelurahan.kecamatan', 'desa_kelurahan.kecamatan.kabupatenKota', 'desa_kelurahan.kecamatan.kabupatenKota.provinsi'])->where(function ($query) use ($idDesaKelurahan) {
            if ($idDesaKelurahan) {
                $query->where('desa_kelurahan_id', $idDesaKelurahan);
            }
        })->orderBy('nama_lokasi_survey', 'asc')->get();

        if ($idLokasiSurvey) {
            $lokasiSurveyHapus = LokasiSurvey::with(['desa_kelurahan', 'desa_kelurahan.kecamatan', 'desa_kelurahan.kecamatan.kabupatenKota', 'desa_kelurahan.kecamatan.kabupatenKota.provinsi'])->where('id', $idLokasiSurvey)->withTrashed()->first();
            if ($lokasiSurveyHapus->trashed()) {
                $lokasiSurvey->push($lokasiSurveyHapus);
            }
        }

        return response()->json($lokasiSurvey);
    }

    public function listSurveyor(Request $request)
    {
        $dpl = Profile::where('id', $request->idSupervisor)->first();
        $idProfileSurveyor = $request->idProfileSurveyor;
        $surveyor = Profile::with('user')->where('institusi_id', $dpl->institusi_id)
            ->whereHas('user', function ($query) {
                $query->where('role', '=', 'Surveyor');
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        if ($idProfileSurveyor) {
            $surveyorHapus = Profile::with('user')->where('institusi_id', $dpl->institusi_id)->where('id', $idProfileSurveyor)->whereHas('user', function ($query) {
                $query->where('role', '=', 'Surveyor');
            })->withTrashed()->first();
            if ($surveyorHapus->trashed()) {
                $surveyor->push($surveyorHapus);
            }
        }

        return response()->json($surveyor);
    }

    public function listSupervisor(Request $request)
    {
        $idInstitusi = $request->idInstitusi;
        $supervisor = Profile::where('institusi_id', $idInstitusi)->whereHas('user', function ($query) {
            $query->where('role', 'Supervisor');
        })->get();

        return response()->json($supervisor);
    }
}
