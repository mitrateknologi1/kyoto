<?php

namespace App\Http\Controllers\masterData\wilayah;

use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Models\Desa_kelurahan;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DesaKelurahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Desa_kelurahan::where('kecamatan_id', $request->kecamatan)->orderBy('nama', 'asc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $actionBtn = '<button class="btn btn-sm btn-primary text-white" id="btn-status" style="cursor: pointer;" data-status="' . $row->status . '" data-nama="' . $row->nama . '" value="' . $row->id . '">Aktif</button>';
                    } else {
                        $actionBtn = '<button class="btn btn-sm btn-danger text-white" id="btn-status" style="cursor: pointer;" data-status="' . $row->status . '" data-nama="' . $row->nama . '" value="' . $row->id . '">Nonaktif</button>';
                    }
                    return $actionBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                        <div class="row text-center justify-content-center">';
                    $actionBtn .= '
                            <button href="' . route('profile.edit', $row->id) . '" id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah" value="' . $row->id . '"><i class="fas fa-edit"></i></button>
                            <button id="btn-delete" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>';
                    return $actionBtn;
                })

                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $kecamatan = Kecamatan::find($request->kecamatan);
        return view('pages.masterData.wilayah.desaKelurahan', compact('kecamatan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Desa/Kelurahan tidak boleh kosong',
                'nama.unique' => 'Nama Desa/Kelurahan sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $desaKelurahan = new Desa_kelurahan();
        $desaKelurahan->nama = $request->nama;
        $desaKelurahan->kecamatan_id = $request->kecamatan;
        $desaKelurahan->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DesaKelurahan  $desaKelurahan
     * @return \Illuminate\Http\Response
     */
    public function show(DesaKelurahan $desaKelurahan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DesaKelurahan  $desaKelurahan
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $desa = Desa_kelurahan::find($request->desaKelurahan);
        return response()->json($desa);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DesaKelurahan  $desaKelurahan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $desaKelurahan = Desa_kelurahan::find($request->desaKelurahan);
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Desa/Kelurahan tidak boleh kosong',
                'nama.unique' => 'Nama Desa/Kelurahan sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $desaKelurahan->nama = $request->nama;
        $desaKelurahan->kecamatan_id = $request->kecamatan;
        $desaKelurahan->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DesaKelurahan  $desaKelurahan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $desaKelurahan = Desa_kelurahan::find($request->desaKelurahan);
        $desaKelurahan->delete();

        return response()->json(['status' => 'success']);
    }

    public function status(Request $request, Desa_kelurahan $desaKelurahan)
    {
        $desaKelurahan->update(['status' => $request->status]);
        return response()->json(['status' => 'success']);
    }
}
