<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;





class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso',
            'user' => $user,
            'token' => $token,
        ], 201);
    }


    public function login(LoginRequest $request)
    {

        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('token', $token, 60 * 24);
            // Devolver el token, establecer la cookie y los datos del usuario
            return response()->json([
                "token" => $token,
                "user" => $user
            ], Response::HTTP_OK)->withCookie($cookie);
        } else {
            // Verificar si el error es debido a un correo electrónico o contraseña incorrectos
            $user = User::where('email', $request->email)->first(); // Buscar el usuario por correo electrónico

            if ($user) {
                // El correo electrónico existe, por lo tanto, la contraseña es incorrecta
                return response(["message" => "Contraseña incorrecta"], Response::HTTP_UNAUTHORIZED);
            } else {
                // El correo electrónico no existe
                return response(["message" => "Correo electrónico no existe"], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete(); // Eliminar todos los tokens del usuario


        return response(["message" => "Cierre de sesión exitoso"], Response::HTTP_OK);
    }
    public function userProfile(Request $request)
    {
        return response()->json([
            "message" => "Perfil de usuario",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }
    public function updateUser(Request $request, $id)
    {
        // Buscar el usuario a actualizar
        $user = User::findOrFail($id);

        // Validar los datos de entrada
        $request->validate([
            "name" => 'required',
            "email" => "required|email|unique:users,email,$user->id", // Asegurar que el correo electrónico sea único excepto para el usuario actual
            "password" => 'nullable|confirmed' // El campo de contraseña es opcional
        ]);

        // Actualizar los campos del usuario
        $user->name = $request->name;
        $user->email = $request->email;

        // Si se proporciona una nueva contraseña, se actualiza
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Guardar los cambios en la base de datos
        $user->save();

        // Devolver la respuesta
        return response()->json([
            "message" => "Usuario actualizado exitosamente",
            "user" => $user
        ], Response::HTTP_OK);
    }

    public function deleteUser($id)
    {
        // Buscar el usuario a eliminar
        $user = User::findOrFail($id);

        // Eliminar el usuario
        $user->delete();

        // Devolver la respuesta
        return response()->json([
            "message" => "Usuario eliminado exitosamente"
        ], Response::HTTP_OK);
    }

}
