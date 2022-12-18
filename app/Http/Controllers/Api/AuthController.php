<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use PhpParser\Node\Expr\FuncCall;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => ['required']
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $registrationData['password'] = bcrypt($request->password);

        $user = User::create($registrationData);

        return response()->json([
            'success' => true,
            'message' => 'Register Success!',
            'user'    => $user  
        ]);
        
    }

    public function login(Request $request){
        $loginData=$request->all();

        $validate=Validator::make($loginData,[
            'email'=>'required|email:rfc,dns',
            'password'=>['required'],
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);

        if(!Auth::attempt(($loginData)))
            return response(['invalid' => true,'message'=>'Invalid Credentials'],401);
       /** @var \App\Models\User $user **/
            $user=Auth::user();
            $token=$user->createToken('Authentication Token')->accessToken;

            return response()->json([
                'success' => true,
                'message'=>'Authenticated',
                'user'=>$user,
                'token_type'=>'Bearer',
                'access_token'=>$token
            ]);
    }

    public function logout(){
         /** @var \App\Models\User $user **/
        $user = Auth::user()->token();
        $user->revoke();
         return response()->json([
            'success' => true,
            'message'=>'User Logout Success'
         ]);
    }

    public function update(Request $request, $id){
        $user = User::find($id); 
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        } 
        
        $updateData = $request->all(); 
        $validate = Validator::make($updateData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => ['required']
            
        ]); //membuat rule validasi input

        if($validate->fails())
        return response()->json($validate->errors(), 400);

        $updateData['password'] = bcrypt($request->password);
        $user->name = $updateData['name'];
        $user->email = $updateData['email'];   
        $user->password = $updateData['password'];     
        if($user->save()){
            return response()->json([               
                'message' => 'Update User Success!',
                'user'    => $user  
            ],200);
        }
        return response()->json([
            'message' => 'Update User Failed',
            'data' => null
        ], 400); 
    }

    public function show($id){
        $user = User::find($id);

        if(!is_null($user)){
            return response([
                'message' => 'Retrieve User Success',
                'data' => $user
            ], 200);
        } 

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 400); 
    }
}