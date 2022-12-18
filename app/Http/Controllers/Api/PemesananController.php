<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Validator;

class PemesananController extends Controller
{
    public function index()
    {
        $pemesanans=Pemesanan::all();

        if(count($pemesanans)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$pemesanans
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],400);
    }


    public function store(Request $request)
    {
        $carbon=\Carbon\Carbon::now();
        $dateY=$carbon->format('y');
        $dateM=$carbon->format('m');
        $dateD=$carbon->format('d');
        $totalBarang=sprintf('%03d',(Pemesanan::all()->count())+1);
        $stringKode='PMSN-'.$dateY.$dateM.$dateD.'-'.$totalBarang;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'idUser'=>'required',
            'idProduct'=>'required',
            'nama_barang'=>'required',
            'jumlah'=>'required|numeric',
            'status' => 'required',
            'namauser'=>'required',
            'harga'=>'required'
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
        
            
        $pemesanan=Pemesanan::create($storeData+['kode'=>$stringKode]);
            return response([
                'message'=>'add pemesanan success',
                'data'=>$pemesanan
            ],200);
    }

    public function show($id)
    {
        $pemesanan=Pemesanan::find($id);

        if(!is_null($pemesanan)){
            return response([
                'message'=>'retrieve pemesanan success',
                'data'=>$pemesanan
            ],200);
        }

        return response([
            'message'=>'Pemesanan not found',
            'data'=>null
        ],404);
    }

    public function showByUser($id){
        $pemesanan = Pemesanan::where('idUser', $id)->get(); // mencari data berdasarkan id

        if(count($pemesanan)>0){
            return response([
                'message' => 'Retrieve Pemesanan Success',
                'data' => $pemesanan
            ], 200);
        } //return data yang ditemukan dalam bentuk json

        return response([
            'message' => 'Pemesanan Not Found',
            'data' => null
        ],); //return message data tidak ditemukan
    }

   
    public function update(Request $request, $id)
    {
        $pemesanan = Pemesanan::find($id);
        if(is_null($pemesanan)){
            return response([
                'message' => 'Pemesanan Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'jumlah'=>'required|numeric',
            'harga'=>'required|numeric',
            'status' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $pemesanan->jumlah = $updateData['jumlah'];
        $pemesanan->harga = $updateData['harga'];
        $pemesanan->status = $updateData['status'];

        if($pemesanan->save()){
            return response([
                'message' => 'Update Pemesanan Success',
                'data' => $pemesanan
            ], 200);
        }

        return response([
            'message' => 'Update Pemesanan Failed',
            'data' => null
        ], 400);

    }

    
    public function destroy($id)
    {
        $pemesanan=Pemesanan::find($id);

        if(is_null($pemesanan)){
            return response([
                'message'=>'Pemesanan not found',
                'data'=>null
            ],404);
        }

        if($pemesanan->delete()){
            return response([
                'message'=>'delete Pemesanan success',
                'data'=>$pemesanan
            ],200);
        }

        return response([
            'message'=>'delete Pemesanan failed',
            'data'=>null
        ],400);
    }
}
