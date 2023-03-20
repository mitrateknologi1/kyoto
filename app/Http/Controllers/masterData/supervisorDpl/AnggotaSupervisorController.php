<?php

namespace App\Http\Controllers\masterData\supervisorDpl;

use App\Http\Controllers\Controller;
use App\Models\AnggotaSupervisor;
use App\Models\LokasiSurveySupervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AnggotaSupervisorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lokasiSurveySupervisor = LokasiSurveySupervisor::where('id', $request->lokasiSurveySupervisor)->first();
        if (!$lokasiSurveySupervisor) {
            return redirect()->back();
        }

        if ($request->ajax()) {
            $data = AnggotaSupervisor::where('profile_dpl', $lokasiSurveySupervisor->profile_id)->where('lokasi_survey_supervisor_id', $lokasiSurveySupervisor->id)->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return $row->profileSurveyor->nama_lengkap;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                        <div class="row text-center justify-content-center">';
                    $actionBtn .= '
                            <button id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" value="' . $row->id . '" ><i class="fas fa-edit"></i></button>';
                    $actionBtn .= '<button id="btn-delete" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" > <i class="fas fa-trash-alt"></i></button>';
                    $actionBtn .= '</div>';
                    if (Auth::user()->role == 'Admin') {
                        return $actionBtn;
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['action', 'total'])
                ->make(true);
        }
        return view('pages.masterData.supervisorDpl.anggotaSupervisor.index', compact(['lokasiSurveySupervisor']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lokasiSurveySupervisor = LokasiSurveySupervisor::where('id', $request->lokasiSurveySupervisor)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'profile_surveyor' => ['required', Rule::unique('anggota_supervisor')
                    ->where(function ($query) use ($lokasiSurveySupervisor) {
                        return $query->where('profile_dpl', $lokasiSurveySupervisor->profile_id)
                            ->where('lokasi_survey_supervisor_id', $lokasiSurveySupervisor->id);
                    })
                    ->withoutTrashed()],
            ],
            [
                'profile_surveyor.required' => 'Surveyor / mahasiswa tidak boleh kosong',
                'profile_surveyor.unique' => 'Surveyor / mahasiswa sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $anggotaSupervisor = new AnggotaSupervisor();
        $anggotaSupervisor->profile_dpl = $lokasiSurveySupervisor->profile_id;
        $anggotaSupervisor->profile_surveyor = $request->profile_surveyor;
        $anggotaSupervisor->lokasi_survey_supervisor_id = $lokasiSurveySupervisor->id;
        $anggotaSupervisor->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AnggotaSupervisor  $anggotaSupervisor
     * @return \Illuminate\Http\Response
     */
    public function show(AnggotaSupervisor $anggotaSupervisor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AnggotaSupervisor  $anggotaSupervisor
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $anggotaSupervisor = AnggotaSupervisor::where('id', $request->anggotaSupervisor)->first();
        return response()->json($anggotaSupervisor);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AnggotaSupervisor  $anggotaSupervisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $anggotaSupervisor = AnggotaSupervisor::where('id', $request->anggotaSupervisor)->first();
        $lokasiSurveySupervisor = LokasiSurveySupervisor::where('id', $request->lokasiSurveySupervisor)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'profile_surveyor' => ['required', Rule::unique('anggota_supervisor')
                    ->where(function ($query) use ($lokasiSurveySupervisor) {
                        return $query->where('profile_dpl', $lokasiSurveySupervisor->profile_id)
                            ->where('lokasi_survey_supervisor_id', $lokasiSurveySupervisor->id);
                    })
                    ->ignore($anggotaSupervisor->id)
                    ->withoutTrashed()],
            ],
            [
                'profile_surveyor.required' => 'Surveyor / mahasiswa tidak boleh kosong',
                'profile_surveyor.unique' => 'Surveyor / mahasiswa sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $anggotaSupervisor->profile_dpl = $lokasiSurveySupervisor->profile_id;
        $anggotaSupervisor->profile_surveyor = $request->profile_surveyor;
        $anggotaSupervisor->lokasi_survey_supervisor_id = $lokasiSurveySupervisor->id;
        $anggotaSupervisor->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AnggotaSupervisor  $anggotaSupervisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $anggotaSupervisor = AnggotaSupervisor::where('id', $request->anggotaSupervisor)->first();
        $anggotaSupervisor->delete();

        return response()->json(['status' => 'success']);
    }
}
