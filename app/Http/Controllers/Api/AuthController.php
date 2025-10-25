<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request)
    {
        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'bio' => $request->bio,
            'avatar' => $request->avatar,
            'is_active' => true,
        ]);

        // Generar token de acceso con expiración de 5 minutos
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 300 // 5 minutos en segundos
            ]
        ], 201);
    }

    /**
     * Inicio de sesión de usuario
     */
    public function login(Request $request)
    {
        // Validación de credenciales
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar credenciales
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo'
            ], 403);
        }

        // Generar nuevo token de acceso
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 300 // 5 minutos en segundos
            ]
        ], 200);
    }

    /**
     * Renovar token de acceso
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        // Eliminar tokens anteriores
        $user->tokens()->delete();

        // Generar nuevo token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado exitosamente',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 300 // 5 minutos en segundos
            ]
        ], 200);
    }

    /**
     * Cerrar sesión de usuario
     */
    public function logout(Request $request)
    {
        // Eliminar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }
}
