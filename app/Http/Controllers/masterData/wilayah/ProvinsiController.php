<?php

namespace App\Http\Controllers\masterData\wilayah;

use App\Models\Provinsi;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Models\DesaKelurahan;
use App\Models\KabupatenKota;
use App\Models\Desa_kelurahan;
use App\Models\Kabupaten_kota;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct()
    // {
    //     $this->middleware('profil_ada');
    // }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Provinsi::orderBy('nama', 'asc')->get();
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
                            <a href="' . url('wilayah-kabupaten/' . $row->id) . '" class="btn btn-info btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Lihat"><i class="fas fa-eye"></i></a>
                            <button id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah" value="' . $row->id . '"><i class="fas fa-edit"></i></button>
                            <button id="btn-delete" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('pages.masterData.wilayah.provinsi');
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
                'nama.required' => 'Nama Provinsi tidak boleh kosong',
                'nama.unique' => 'Nama Provinsi sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $provinsi = new Provinsi();
        $provinsi->nama = $request->nama;
        $provinsi->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\Response
     */
    public function show(Provinsi $provinsi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\Response
     */
    public function edit(Provinsi $wilayah_provinsi)
    {
        return response()->json($wilayah_provinsi);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provinsi $wilayah_provinsi)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required',
            ],
            [
                'nama.required' => 'Nama Provinsi tidak boleh kosong',
                'nama.unique' => 'Nama Provinsi sudah ada',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $wilayah_provinsi->nama = $request->nama;
        $wilayah_provinsi->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provinsi $wilayah_provinsi)
    {
        foreach ($wilayah_provinsi->kabupatenKota as $kab) {
            foreach ($kab->kecamatan as $kec) {
                $kec->desaKelurahan()->delete();
            }
            $kab->kecamatan()->delete();
        }
        $wilayah_provinsi->kabupatenKota()->delete();
        $wilayah_provinsi->delete();

        return response()->json(['status' => 'success']);
    }

    public function status(Request $request, Provinsi $wilayah_provinsi)
    {
        $wilayah_provinsi->update(['status' => $request->status]);
        $wilayah_provinsi->kabupatenKota()->update(['status' => $request->status]);
        foreach ($wilayah_provinsi->kabupatenKota()->get() as $kab) {
            $kab->kecamatan()->update(['status' => $request->status]);
            foreach ($kab->kecamatan()->get() as $kec) {
                $kec->desaKelurahan()->update(['status' => $request->status]);
            }
        }
        return response()->json(['status' => 'success']);
    }
}
