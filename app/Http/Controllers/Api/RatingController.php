<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    /**
     * Mostrar valoraciones de un producto específico
     */
    public function index(Request $request)
    {
        $productId = $request->get('product_id');
        $perPage = $request->get('per_page', 10);

        $query = Rating::with(['user', 'product']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $ratings = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de valoraciones obtenida exitosamente',
            'data' => $ratings
        ], 200);
    }

    /**
     * Crear una nueva valoración para un producto
     */
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
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

        // Verificar que el usuario no sea el propietario del producto
        if ($product->user_id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes valorar tu propio producto'
            ], 403);
        }

        // Verificar si el usuario ya ha valorado este producto
        $existingRating = Rating::where('user_id', $request->user()->id)
                               ->where('product_id', $request->product_id)
                               ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has valorado este producto'
            ], 409);
        }

        // Crear nueva valoración
        $rating = Rating::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valoración creada exitosamente',
            'data' => $rating->load(['user', 'product'])
        ], 201);
    }

    /**
     * Mostrar una valoración específica
     */
    public function show($id)
    {
        $rating = Rating::with(['user', 'product'])->find($id);

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Valoración no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Valoración obtenida exitosamente',
            'data' => $rating
        ], 200);
    }

    /**
     * Actualizar una valoración específica
     */
    public function update(Request $request, $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Valoración no encontrada'
            ], 404);
        }

        // Verificar que el usuario sea el propietario de la valoración
        if ($rating->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para modificar esta valoración'
            ], 403);
        }

        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar campos
        $updateData = $request->only(['rating', 'review']);
        $rating->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Valoración actualizada exitosamente',
            'data' => $rating->fresh()->load(['user', 'product'])
        ], 200);
    }

    /**
     * Eliminar una valoración específica
     */
    public function destroy(Request $request, $id)
    {
        $rating = Rating::find($id);

        if (!$rating) {
            return response()->json([
                'success' => false,
                'message' => 'Valoración no encontrada'
            ], 404);
        }

        // Verificar que el usuario sea el propietario de la valoración
        if ($rating->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para eliminar esta valoración'
            ], 403);
        }

        // Eliminar la valoración
        $rating->delete();

        return response()->json([
            'success' => true,
            'message' => 'Valoración eliminada exitosamente'
        ], 200);
    }

    /**
     * Obtener estadísticas de valoraciones de un producto
     */
    public function productStats($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $ratingsStats = Rating::where('product_id', $productId)
                             ->selectRaw('
                                 AVG(rating) as average_rating,
                                 COUNT(*) as total_ratings,
                                 SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
                                 SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
                                 SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
                                 SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
                                 SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                             ')
                             ->first();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas de valoraciones obtenidas exitosamente',
            'data' => [
                'product' => $product,
                'stats' => $ratingsStats
            ]
        ], 200);
    }
}
