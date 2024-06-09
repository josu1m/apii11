<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;




class AppServiceProvider extends ServiceProvider
{
    public function registro(Request $request)
    {
        // Validación de los datos
        $request->validate([
            "name" => 'required',
            "email" => "required|email|unique:users",
            "password" => 'required|confirmed'
        ]);

        // Alta del usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Generar token para el usuario
        $token = $user->createToken('token')->plainTextToken;

        // Crear la cookie de sesión con el token
        $cookie = cookie('token', $token, 60 * 24);

        // Respuesta con el token y el usuario registrado
        return response()->json([
            "message" => "Registro exitoso",
            "user" => $user,
            "token" => $token
        ], Response::HTTP_CREATED)->withCookie($cookie);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('token', $token, 60 * 24); // Cookie con el token

            // Devolver el token, establecer la cookie y además devolver los datos del usuario
            return response()->json([
                "token" => $token,
                "user" => $user // Incluir los datos del usuario en la respuesta
            ], Response::HTTP_OK)->withCookie($cookie);
        } else {
            return response(["message" => "Credenciales inválidas"], Response::HTTP_UNAUTHORIZED);
        }
    }
}
