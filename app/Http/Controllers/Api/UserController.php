<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Mostrar lista de usuarios con paginación
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $users = User::with(['products', 'ratings', 'comments'])
                    ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de usuarios obtenida exitosamente',
            'data' => $users
        ], 200);
    }

    /**
     * Crear un nuevo usuario (solo para administradores)
     */
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|url',
            'is_active' => 'nullable|boolean',
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
            'is_active' => $request->get('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $user
        ], 201);
    }

    /**
     * Mostrar un usuario específico
     */
    public function show($id)
    {
        $user = User::with(['products', 'ratings', 'comments'])->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Agregar estadísticas del usuario
        $userStats = [
            'total_products' => $user->products->count(),
            'total_ratings' => $user->ratings->count(),
            'total_comments' => $user->comments->count(),
            'average_rating_given' => $user->ratings->avg('rating'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Usuario obtenido exitosamente',
            'data' => [
                'user' => $user,
                'stats' => $userStats
            ]
        ], 200);
    }

    /**
     * Actualizar un usuario específico
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar campos
        $updateData = $request->only([
            'name', 'email', 'phone', 'birth_date', 
            'gender', 'bio', 'avatar', 'is_active'
        ]);

        // Encriptar contraseña si se proporciona
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user->fresh()
        ], 200);
    }

    /**
     * Eliminar un usuario específico
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Eliminar el usuario (esto también eliminará sus productos, ratings y comentarios por CASCADE)
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ], 200);
    }

    /**
     * Buscar usuarios por nombre o email
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Parámetro de búsqueda requerido'
            ], 400);
        }

        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Búsqueda completada exitosamente',
            'data' => $users
        ], 200);
    }
}
