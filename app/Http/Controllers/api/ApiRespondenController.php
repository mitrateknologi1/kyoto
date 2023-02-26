<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Responden;

class ApiRespondenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $withTrashed = $request->withTrashed;
        if(!$withTrashed){
            $responden = Responden::orderBy('id', 'desc')->whereYear('created_at', '2023')->get();
        }else {
            $responden = Responden::withTrashed()->orderBy('id', 'desc')->whereYear('created_at', '2023')->get();
        }
        if($responden){
            return response([
                'message' => 'OK',
                'data' => $responden
            ], 200);
        } else {
            return response([
                'message' => 'data not found.'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kepala_keluarga' => 'required',
            'kartu_keluarga' => 'required|numeric|unique:responden',
            'alamat' => 'required',
            'provinsi_id' => 'required|numeric',
            'kabupaten_kota_id' => 'required|numeric',
            'kecamatan_id' => 'required|numeric',
            'desa_kelurahan_id' => 'required|numeric'
        ]);

        if($request->kode_unik != null) {
            $kodeUnikResponden = $request->kode_unik;
        } else {
            $kodeUnikResponden = $this->generateKodeUnik();
        }

        $data = [
            'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
            'kartu_keluarga' => $request->kartu_keluarga,
            'alamat' => $request->alamat,
            'provinsi_id' => $request->provinsi_id,
            'kabupaten_kota_id' => $request->kabupaten_kota_id,
            'kecamatan_id' => $request->kecamatan_id,
            'desa_kelurahan_id' => $request->desa_kelurahan_id,
            'nomor_hp' => $request->nomor_hp,
            'kode_unik' => $kodeUnikResponden
        ];

        $data = Responden::create($data);
        if($data){
            return response([
                'data' => $data,
                'message' => 'data created.'
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generateKodeUnik()
    {
        do {
            $code = random_int(10000000, 99999999);
        } while (Responden::where("kode_unik", "=", $code)->first());

        return $code;
    }
}
