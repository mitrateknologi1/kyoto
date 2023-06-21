<?php

namespace App\Http\Controllers\masterData\wilayah;

use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Models\KabupatenKota;
use App\Models\Kabupaten_kota;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kecamatan::where('kabupaten_kota_id', $request->kabupatenKota)->orderBy('nama', 'asc')->get();
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
                            <a href="' . url('wilayah-desa/' . $row->id) . '" class="btn btn-info btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Lihat"><i class="fas fa-eye"></i></a>
                            <button href="' . route('profile.edit', $row->id) . '" id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah" value="' . $row->id . '"><i class="fas fa-edit"></i></button>
                            <button id="btn-delete" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>';
                    return $actionBtn;
                })

                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $kabupatenKota = Kabupaten_kota::find($request->kabupatenKota);
        return view('pages.masterData.wilayah.kecamatan', compact('kabupatenKota'));
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
                'nama.required' => 'Nama Kecamatan tidak boleh kosong',
                'nama.unique' => 'Nama Kecamatan sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $kabupatenKota = new Kecamatan();
        $kabupatenKota->nama = $request->nama;
        $kabupatenKota->kabupaten_kota_id = $request->kabupaten;
        $kabupatenKota->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\Response
     */
    public function show(Kecamatan $kecamatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $kecamatan = Kecamatan::find($request->kecamatan);
        return response()->json($kecamatan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $kecamatan = Kecamatan::find($request->kecamatan);
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Kecamatan tidak boleh kosong',
                'nama.unique' => 'Nama Kecamatan sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $kecamatan->nama = $request->nama;
        $kecamatan->kabupaten_kota_id = $request->kabupaten;


        $kecamatan->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $kecamatan = Kecamatan::find($request->kecamatan);
        $kecamatan->delete();

        $kecamatan->desaKelurahan()->delete();

        return response()->json(['status' => 'success']);
    }

    public function status(Request $request, Kecamatan $kecamatan)
    {
        $kecamatan->update(['status' => $request->status]);
        $kecamatan->desaKelurahan()->update(['status' => $request->status]);
        return response()->json(['status' => 'success']);
    }
}
