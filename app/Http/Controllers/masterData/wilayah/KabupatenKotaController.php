<?php

namespace App\Http\Controllers\masterData\wilayah;

use App\Models\Provinsi;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Models\DesaKelurahan;
use App\Models\Kabupaten_kota;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KabupatenKotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kabupaten_kota::where('provinsi_id', $request->wilayah_provinsi)->orderBy('nama', 'asc')->get();
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
                            <a href="' . url('wilayah-kecamatan/' . $row->id) . '" class="btn btn-info btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Lihat"><i class="fas fa-eye"></i></a>
                            <button id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah" value="' . $row->id . '"><i class="fas fa-edit"></i></button>
                            <button id="btn-delete" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $provinsi = Provinsi::find($request->wilayah_provinsi);
        return view('pages.masterData.wilayah.kabupatenKota', compact('provinsi'));
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
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Kabupaten / Kota tidak boleh kosong',
                'nama.unique' => 'Nama Kabupaten / Kota sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $kabupatenKota = new Kabupaten_kota();
        $kabupatenKota->nama = $request->nama;
        $kabupatenKota->provinsi_id = $request->provinsi;
        $kabupatenKota->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KabupatenKota  $kabupatenKota
     * @return \Illuminate\Http\Response
     */
    public function show(KabupatenKota $kabupatenKota)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KabupatenKota  $kabupatenKota
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $kabupatenKota = Kabupaten_kota::find($request->kabupatenKota);
        return response()->json($kabupatenKota);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KabupatenKota  $kabupatenKota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $kabupatenKota = Kabupaten_kota::find($request->kabupatenKota);
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Kabupaten / Kota tidak boleh kosong',
                'nama.unique' => 'Nama Kabupaten / Kota sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $kabupatenKota->nama = $request->nama;
        $kabupatenKota->provinsi_id = $request->provinsi;
        $kabupatenKota->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KabupatenKota  $kabupatenKota
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $kabupatenKota = Kabupaten_kota::with('kecamatan')->find($request->kabupatenKota);
        foreach ($kabupatenKota->kecamatan as $kec) {
            $kec->desaKelurahan()->delete();
        }
        $kabupatenKota->kecamatan()->delete();
        $kabupatenKota->delete();

        return response()->json(['status' => 'success']);
    }

    public function status(Request $request, Kabupaten_kota $kabupatenKota)
    {
        $kabupatenKota->update(['status' => $request->status]);
        $kabupatenKota->kecamatan()->update(['status' => $request->status]);
        foreach ($kabupatenKota->kecamatan as $kec) {
            $kec->desaKelurahan()->update(['status' => $request->status]);
        }
        return response()->json(['status' => 'success']);
    }
}
