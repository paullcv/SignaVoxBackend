<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        //validar datos
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        //crear nuevo usuario
        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
        ]);
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $response = ['user' => $user, 'token' => $token];
        return response()->json($response, 200);
    }

    public function login(Request $req)
    {
        //validar datos
        $rules = [
            'email' => 'required',
            'password' => 'required|string'
        ];
        $req->validate($rules);
        //encontrar el email en la tabla de usuarios
        $user = User::where('email', $req->email)->first();
        //si el email se encontro 
        if ($user && Hash::check($req->password, $user->password)) {
            $token = $user->createToken('Personal Acces Token')->plainTextToken;
            $response = [
                'message' => 'Inicio de session exitoso',
                'user' => $user, 
                'token' => $token
            ];
            return response()->json($response, 200);
        }
        $response = ['message' => 'Email o contraseña incorrecta'];
        return response()->json($response, 400);
    }
}
