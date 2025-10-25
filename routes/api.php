<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserStatsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas protegidas por autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de autenticación para usuarios autenticados
    Route::prefix('auth')->group(function () {
        Route::post('refresh', [AuthController::class, 'refreshToken']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // === API DE GESTIÓN DE USUARIOS ===
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('search', [UserController::class, 'search']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy']);
    });

    // Rutas de estadísticas de usuarios
    Route::prefix('users/stats')->group(function () {
        Route::get('/', [UserStatsController::class, 'index']);
        Route::get('daily', [UserStatsController::class, 'dailyRegistrations']);
        Route::get('weekly', [UserStatsController::class, 'weeklyRegistrations']);
        Route::get('monthly', [UserStatsController::class, 'monthlyRegistrations']);
        Route::get('demographics', [UserStatsController::class, 'demographics']);
        Route::get('most-active', [UserStatsController::class, 'mostActiveUsers']);
    });

    // === API DE GESTIÓN DE PRODUCTOS ===
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('search', [ProductController::class, 'search']);
        Route::get('top-rated', [ProductController::class, 'topRated']);
        Route::get('{id}', [ProductController::class, 'show']);
        Route::put('{id}', [ProductController::class, 'update']);
        Route::delete('{id}', [ProductController::class, 'destroy']);
    });

    // Rutas de valoraciones
    Route::prefix('ratings')->group(function () {
        Route::get('/', [RatingController::class, 'index']);
        Route::post('/', [RatingController::class, 'store']);
        Route::get('product/{productId}/stats', [RatingController::class, 'productStats']);
        Route::get('{id}', [RatingController::class, 'show']);
        Route::put('{id}', [RatingController::class, 'update']);
        Route::delete('{id}', [RatingController::class, 'destroy']);
    });

    // Rutas de comentarios
    Route::prefix('comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
        Route::get('my-products', [CommentController::class, 'myProductsComments']);
        Route::get('{id}', [CommentController::class, 'show']);
        Route::put('{id}', [CommentController::class, 'update']);
        Route::delete('{id}', [CommentController::class, 'destroy']);
        Route::patch('{id}/toggle-approval', [CommentController::class, 'toggleApproval']);
    });
});

// === RUTAS PÚBLICAS PARA TESTING ===
Route::prefix('test')->group(function () {
    
    // Endpoints de prueba para productos (sin autenticación)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/top-rated', [ProductController::class, 'topRated']);
    
    // Endpoints de prueba para valoraciones (sin autenticación)
    Route::get('ratings', [RatingController::class, 'index']);
    Route::get('ratings/product/{productId}/stats', [RatingController::class, 'productStats']);
    
    // Endpoints de prueba para comentarios (sin autenticación)
    Route::get('comments', [CommentController::class, 'index']);
    
    // Endpoint de información de la API
    Route::get('info', function () {
        return response()->json([
            'success' => true,
            'message' => 'API de Gestión de Usuarios y Productos',
            'version' => '1.0.0',
            'description' => 'Esta API permite gestionar usuarios y productos con autenticación mediante tokens.',
            'features' => [
                'Gestión completa de usuarios (CRUD)',
                'Estadísticas de usuarios por día, semana y mes',
                'Gestión completa de productos (CRUD)',
                'Sistema de valoraciones y comentarios',
                'Autenticación con tokens de 5 minutos de expiración',
                'Búsqueda y filtrado de productos',
                'Productos mejor valorados'
            ],
            'endpoints' => [
                'auth' => [
                    'POST /api/auth/register' => 'Registro de usuario',
                    'POST /api/auth/login' => 'Inicio de sesión',
                    'POST /api/auth/refresh' => 'Renovar token (requiere auth)',
                    'POST /api/auth/logout' => 'Cerrar sesión (requiere auth)',
                    'GET /api/auth/me' => 'Información del usuario autenticado'
                ],
                'users' => [
                    'GET /api/users' => 'Listar usuarios (requiere auth)',
                    'POST /api/users' => 'Crear usuario (requiere auth)',
                    'GET /api/users/{id}' => 'Ver usuario específico (requiere auth)',
                    'PUT /api/users/{id}' => 'Actualizar usuario (requiere auth)',
                    'DELETE /api/users/{id}' => 'Eliminar usuario (requiere auth)',
                    'GET /api/users/search?q={query}' => 'Buscar usuarios (requiere auth)'
                ],
                'user_stats' => [
                    'GET /api/users/stats' => 'Estadísticas generales (requiere auth)',
                    'GET /api/users/stats/daily' => 'Registros diarios (requiere auth)',
                    'GET /api/users/stats/weekly' => 'Registros semanales (requiere auth)',
                    'GET /api/users/stats/monthly' => 'Registros mensuales (requiere auth)',
                    'GET /api/users/stats/demographics' => 'Estadísticas demográficas (requiere auth)',
                    'GET /api/users/stats/most-active' => 'Usuarios más activos (requiere auth)'
                ],
                'products' => [
                    'GET /api/products' => 'Listar productos (requiere auth)',
                    'POST /api/products' => 'Crear producto (requiere auth)',
                    'GET /api/products/{id}' => 'Ver producto específico (requiere auth)',
                    'PUT /api/products/{id}' => 'Actualizar producto (requiere auth)',
                    'DELETE /api/products/{id}' => 'Eliminar producto (requiere auth)',
                    'GET /api/products/search?q={query}' => 'Buscar productos (requiere auth)',
                    'GET /api/products/top-rated' => 'Productos mejor valorados (requiere auth)'
                ],
                'ratings' => [
                    'GET /api/ratings?product_id={id}' => 'Listar valoraciones (requiere auth)',
                    'POST /api/ratings' => 'Crear valoración (requiere auth)',
                    'GET /api/ratings/{id}' => 'Ver valoración específica (requiere auth)',
                    'PUT /api/ratings/{id}' => 'Actualizar valoración (requiere auth)',
                    'DELETE /api/ratings/{id}' => 'Eliminar valoración (requiere auth)',
                    'GET /api/ratings/product/{productId}/stats' => 'Estadísticas de valoraciones (requiere auth)'
                ],
                'comments' => [
                    'GET /api/comments?product_id={id}' => 'Listar comentarios (requiere auth)',
                    'POST /api/comments' => 'Crear comentario (requiere auth)',
                    'GET /api/comments/{id}' => 'Ver comentario específico (requiere auth)',
                    'PUT /api/comments/{id}' => 'Actualizar comentario (requiere auth)',
                    'DELETE /api/comments/{id}' => 'Eliminar comentario (requiere auth)',
                    'GET /api/comments/my-products' => 'Comentarios de mis productos (requiere auth)',
                    'PATCH /api/comments/{id}/toggle-approval' => 'Aprobar/desaprobar comentario (requiere auth)'
                ],
                'testing' => [
                    'GET /api/test/info' => 'Información de la API',
                    'GET /api/test/products' => 'Listar productos (sin auth)',
                    'GET /api/test/products/{id}' => 'Ver producto (sin auth)',
                    'GET /api/test/ratings' => 'Listar valoraciones (sin auth)',
                    'GET /api/test/comments' => 'Listar comentarios (sin auth)'
                ]
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'expiration' => '5 minutos',
                'refresh_endpoint' => '/api/auth/refresh'
            ]
        ], 200);
    });
});