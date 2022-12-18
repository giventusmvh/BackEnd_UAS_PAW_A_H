<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Distributor;
use Illuminate\Support\Carbon;

class DistributorController extends Controller
{
    public function index()
    {
        $distributors=Distributor::all();

        if(count($distributors)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$distributors
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
        $totalBarang=sprintf('%03d',(Distributor::all()->count())+1);
        $stringKode='DIST-'.$dateY.$dateM.$dateD.'-'.$totalBarang;
        $storeData=$request->all();
        $validate=Validator::make($storeData,[
            'nama_distributor'=>'required|max:60|unique:distributors',
            'daerah'=>'required',
            'nomor_telepon'=>'required|numeric'
        ]);

        if($validate->fails())
        return response()->json($validate->errors(), 400);
        
            $distributor=Distributor::create($storeData+['kode'=>$stringKode]);
            // $distributor=Distributor::create($storeData);
            return response([
                'message'=>'add distributor success',
                'data'=>$distributor
            ],200);
    }

    public function show($id)
    {
        $distributor=Distributor::find($id);

        if(!is_null($distributor)){
            return response([
                'message'=>'retrieve distributor success',
                'data'=>$distributor
            ],200);
        }

        return response([
            'message'=>'Distributor not found',
            'data'=>null
        ],404);
    }

   
    public function update(Request $request, $id)
    {
        $distributor = Distributor::find($id);
        if(is_null($distributor)){
            return response([
                'message' => 'Distributor Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_distributor' => ['required', 'max:60', Rule::unique('distributors')->ignore($distributor)],
            'daerah' => 'required',
            'nomor_telepon' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $distributor->nama_distributor = $updateData['nama_distributor'];
        $distributor->daerah = $updateData['daerah'];
        $distributor->nomor_telepon = $updateData['nomor_telepon'];

        if($distributor->save()){
            return response([
                'message' => 'Update Distributor Success',
                'data' => $distributor
            ], 200);
        }

        return response([
            'message' => 'Update Distributor Failed',
            'data' => null
        ], 400);

    }

    
    public function destroy($id)
    {
        $distributor=Distributor::find($id);

        if(is_null($distributor)){
            return response([
                'message'=>'Distributor not found',
                'data'=>null
            ],404);
        }

        if($distributor->delete()){
            return response([
                'message'=>'delete Distributor success',
                'data'=>$distributor
            ],200);
        }

        return response([
            'message'=>'delete Distributor failed',
            'data'=>null
        ],400);
    }
}
