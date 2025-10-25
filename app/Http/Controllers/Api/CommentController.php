<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Mostrar comentarios de un producto específico
     */
    public function index(Request $request)
    {
        $productId = $request->get('product_id');
        $perPage = $request->get('per_page', 10);
        $approved = $request->get('approved', true);

        $query = Comment::with(['user', 'product']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Filtrar por comentarios aprobados
        if ($approved !== null) {
            $query->where('is_approved', $approved);
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de comentarios obtenida exitosamente',
            'data' => $comments
        ], 200);
    }

    /**
     * Crear un nuevo comentario para un producto
     */
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el producto existe y está activo
        $product = Product::where('id', $request->product_id)
                         ->where('is_active', true)
                         ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado o inactivo'
            ], 404);
        }

        // Crear nuevo comentario
        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'comment' => $request->comment,
            'is_approved' => true, // Por defecto aprobado, puedes cambiar esto según tus necesidades
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comentario creado exitosamente',
            'data' => $comment->load(['user', 'product'])
        ], 201);
    }

    /**
     * Mostrar un comentario específico
     */
    public function show($id)
    {
        $comment = Comment::with(['user', 'product'])->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comentario obtenido exitosamente',
            'data' => $comment
        ], 200);
    }

    /**
     * Actualizar un comentario específico
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el propietario del comentario
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para modificar este comentario'
            ], 403);
        }

        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'comment' => 'sometimes|required|string|max:1000',
            'is_approved' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar campos
        $updateData = $request->only(['comment', 'is_approved']);
        $comment->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Comentario actualizado exitosamente',
            'data' => $comment->fresh()->load(['user', 'product'])
        ], 200);
    }

    /**
     * Eliminar un comentario específico
     */
    public function destroy(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el propietario del comentario o del producto
        $user = $request->user();
        $isOwner = $comment->user_id === $user->id;
        $isProductOwner = $comment->product->user_id === $user->id;

        if (!$isOwner && !$isProductOwner) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para eliminar este comentario'
            ], 403);
        }

        // Eliminar el comentario
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado exitosamente'
        ], 200);
    }

    /**
     * Aprobar o desaprobar un comentario (solo para el propietario del producto)
     */
    public function toggleApproval(Request $request, $id)
    {
        $comment = Comment::with('product')->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comentario no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el propietario del producto
        if ($comment->product->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Solo el propietario del producto puede aprobar/desaprobar comentarios'
            ], 403);
        }

        // Cambiar el estado de aprobación
        $comment->update([
            'is_approved' => !$comment->is_approved
        ]);

        $status = $comment->is_approved ? 'aprobado' : 'desaprobado';

        return response()->json([
            'success' => true,
            'message' => "Comentario {$status} exitosamente",
            'data' => $comment->fresh()->load(['user', 'product'])
        ], 200);
    }

    /**
     * Obtener comentarios de productos del usuario autenticado
     */
    public function myProductsComments(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $comments = Comment::with(['user', 'product'])
                          ->whereHas('product', function($query) use ($request) {
                              $query->where('user_id', $request->user()->id);
                          })
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Comentarios de tus productos obtenidos exitosamente',
            'data' => $comments
        ], 200);
    }
}
