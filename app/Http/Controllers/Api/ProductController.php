<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Mostrar lista de productos con paginación
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $category = $request->get('category');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = Product::with(['user', 'ratings', 'comments'])
                        ->where('is_active', true);

        // Filtrar por categoría si se proporciona
        if ($category) {
            $query->where('category', $category);
        }

        // Aplicar ordenamiento
        if ($sortBy === 'rating') {
            $query->withAvg('ratings', 'rating')
                  ->orderBy('ratings_avg_rating', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate($perPage);

        // Agregar promedio de rating y total de ratings para cada producto
        $products->getCollection()->transform(function ($product) {
            $product->average_rating = $product->averageRating();
            $product->total_ratings = $product->totalRatings();
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Lista de productos obtenida exitosamente',
            'data' => $products
        ], 200);
    }

    /**
     * Crear un nuevo producto
     */
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'sku' => 'required|string|unique:products|max:255',
            'stock' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear nuevo producto
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'brand' => $request->brand,
            'sku' => $request->sku,
            'stock' => $request->get('stock', 0),
            'images' => $request->images,
            'is_active' => $request->get('is_active', true),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $product->load(['user', 'ratings', 'comments'])
        ], 201);
    }

    /**
     * Mostrar un producto específico
     */
    public function show($id)
    {
        $product = Product::with(['user', 'ratings.user', 'comments.user'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Agregar estadísticas del producto
        $product->average_rating = $product->averageRating();
        $product->total_ratings = $product->totalRatings();
        $product->total_comments = $product->comments->count();

        return response()->json([
            'success' => true,
            'message' => 'Producto obtenido exitosamente',
            'data' => $product
        ], 200);
    }

    /**
     * Actualizar un producto específico
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el propietario del producto
        if ($product->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para modificar este producto'
            ], 403);
        }

        // Validación de datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
            'stock' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'url',
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
            'name', 'description', 'price', 'category', 
            'brand', 'sku', 'stock', 'images', 'is_active'
        ]);

        $product->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $product->fresh()->load(['user', 'ratings', 'comments'])
        ], 200);
    }

    /**
     * Eliminar un producto específico
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Verificar que el usuario sea el propietario del producto
        if ($product->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes autorización para eliminar este producto'
            ], 403);
        }

        // Eliminar el producto (esto también eliminará sus ratings y comentarios por CASCADE)
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ], 200);
    }

    /**
     * Buscar productos por nombre, descripción o categoría
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

        $products = Product::with(['user', 'ratings', 'comments'])
                          ->where('is_active', true)
                          ->where(function($q) use ($query) {
                              $q->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('description', 'LIKE', "%{$query}%")
                                ->orWhere('category', 'LIKE', "%{$query}%")
                                ->orWhere('brand', 'LIKE', "%{$query}%");
                          })
                          ->paginate(10);

        // Agregar estadísticas para cada producto
        $products->getCollection()->transform(function ($product) {
            $product->average_rating = $product->averageRating();
            $product->total_ratings = $product->totalRatings();
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Búsqueda completada exitosamente',
            'data' => $products
        ], 200);
    }

    /**
     * Obtener productos con mejor valoración
     */
    public function topRated(Request $request)
    {
        $limit = $request->get('limit', 10);

        $products = Product::with(['user', 'ratings', 'comments'])
                          ->where('is_active', true)
                          ->withAvg('ratings', 'rating')
                          ->having('ratings_avg_rating', '>', 0)
                          ->orderByDesc('ratings_avg_rating')
                          ->limit($limit)
                          ->get();

        // Agregar estadísticas para cada producto
        $products->transform(function ($product) {
            $product->average_rating = $product->averageRating();
            $product->total_ratings = $product->totalRatings();
            return $product;
        });

        return response()->json([
            'success' => true,
            'message' => 'Productos mejor valorados obtenidos exitosamente',
            'data' => $products
        ], 200);
    }
}
