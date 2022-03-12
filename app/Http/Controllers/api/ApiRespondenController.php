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
    public function index()
    {
        $responden = Responden::orderBy('id', 'desc')->get();
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
            'kartu_keluarga' => 'required|numeric|unique:responden',
            'alamat' => 'required',
            'provinsi_id' => 'required|numeric',
            'kabupaten_kota_id' => 'required|numeric',
            'kecamatan_id' => 'required|numeric',
            'desa_kelurahan_id' => 'required|numeric',
            'nomor_hp' => 'numeric'
        ]);

        $data = Responden::create($request->all());
        if($data){
            return response([
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
}