<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(Request $request){
        $validatedData= Validator::make($request->all(),[
            'name' => 'required|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        if($validatedData->fails()){
            return response()->json(
                $validatedData->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $response = [
            'message' => 'Registrasi Berhasil, Silahkan Login!',
            'data' => $user['token'] = $user->createToken('auth_token')->plainTextToken
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function login(Request $request){

        if(Auth::attempt(['email'=>$request->email,'password'=> $request->password])){
            $auth = Auth::user();
            $data['name'] = $auth->name;
            $data['token'] = $auth->createToken('auth_token')->plainTextToken;
            $response = [
                'message' => 'Login Sukses',
                'data' => $data
            ];
            return response()->json($response, Response::HTTP_OK);
        }else{
            $response = [
                'message' => 'Email atau Password Salah!',
                'data' => null
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        


    }
}
