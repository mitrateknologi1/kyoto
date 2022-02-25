<?php

namespace App\Http\Controllers\masterData;

use App\Models\Responden;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ListController;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RespondenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Responden::orderBy('created_at', 'DESC');
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {     
                    $actionBtn = '
                    <div class="row text-center justify-content-center">';
                    $actionBtn .= '
                        <a href="'.route('responden.show', $row->id).'" class="btn btn-info btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Lihat"><i class="fas fa-eye"></i></a>
                        <a href="'.route('responden.edit', $row->id).'" id="btn-edit" class="btn btn-warning btn-sm mr-1 my-1" data-toggle="tooltip" data-placement="top" title="Ubah"><i class="fas fa-edit"></i></a>
                        <button id="btn-delete" onclick="hapus(' . $row->id . ')" class="btn btn-danger btn-sm mr-1 my-1" value="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash"></i></button>
                    </div>';
                return $actionBtn;
            })
            ->rawColumns(['provinsi', 'kabupaten_kota', 'action'])
            ->make(true);
        }
        return view('pages.masterData.responden.index');
    }

    // Khusus Surveyor
    public function pilihResponden(){
        $responden = Responden::latest()->get();
        return view('pages.survey.pilihResponden', compact('responden'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.masterData.responden.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRespondenRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'kartu_keluarga' => ['required', Rule::unique('responden')->withoutTrashed()],
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten_kota' => 'required',
                'kecamatan' => 'required',
                'desa_kelurahan' => 'required',

            ],
            [
                'kartu_keluarga.required' => 'Kartu keluarga tidak boleh kosong',
                'kartu_keluarga.unique' => 'Kartu keluarga sudah terdaftar',
                'alamat.required' => 'Alamat tidak boleh kosong',
                'provinsi.required' => 'Provinsi tidak boleh kosong',
                'kabupaten_kota.required' => 'Kabupaten/Kota tidak boleh kosong',
                'kecamatan.required' => 'Kecamatan tidak boleh kosong',
                'desa_kelurahan.required' => 'Desa/Kelurahan tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $data = [
            'kartu_keluarga' => $request->kartu_keluarga,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'kecamatan' => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'nomor_hp' => $request->nomor_hp,
        ];

        Responden::create($data);

        return response()->json(['success' => 'Success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Responden  $responden
     * @return \Illuminate\Http\Response
     */
    public function show(Responden $responden)
    {
        return view('pages.masterData.responden.show', compact('responden'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Responden  $responden
     * @return \Illuminate\Http\Response
     */
    public function edit(Responden $responden)
    {
       return view('pages.masterData.responden.edit', compact('responden'));    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRespondenRequest  $request
     * @param  \App\Models\Responden  $responden
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Responden $responden)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'kartu_keluarga' => ['required', Rule::unique('responden')->ignore($responden->id)->withoutTrashed()],
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten_kota' => 'required',
                'kecamatan' => 'required',
                'desa_kelurahan' => 'required',

            ],
            [
                'kartu_keluarga.required' => 'Kartu keluarga tidak boleh kosong',
                'kartu_keluarga.unique' => 'Kartu keluarga sudah terdaftar',
                'alamat.required' => 'Alamat tidak boleh kosong',
                'provinsi.required' => 'Provinsi tidak boleh kosong',
                'kabupaten_kota.required' => 'Kabupaten/Kota tidak boleh kosong',
                'kecamatan.required' => 'Kecamatan tidak boleh kosong',
                'desa_kelurahan.required' => 'Desa/Kelurahan tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $data = [
            'kartu_keluarga' => $request->kartu_keluarga,
            'alamat' => $request->alamat,
            'provinsi' => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'kecamatan' => $request->kecamatan,
            'desa_kelurahan' => $request->desa_kelurahan,
            'nomor_hp' => $request->nomor_hp,
        ];

        $responden->update($data);

        return response()->json(['success' => 'Success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Responden  $responden
     * @return \Illuminate\Http\Response
     */
    public function destroy(Responden $responden)
    {
        $responden->delete();

        return response()->json(['success' => 'Success']);
    }
}