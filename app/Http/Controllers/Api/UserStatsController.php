<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserStatsController extends Controller
{
    /**
     * Obtener estadísticas generales de usuarios
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'users_with_products' => User::has('products')->count(),
            'users_with_ratings' => User::has('ratings')->count(),
            'users_with_comments' => User::has('comments')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas generales obtenidas exitosamente',
            'data' => $stats
        ], 200);
    }

    /**
     * Obtener estadísticas de usuarios registrados por día
     */
    public function dailyRegistrations(Request $request)
    {
        $days = $request->get('days', 30); // Por defecto últimos 30 días
        
        $dailyStats = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as users_count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas diarias obtenidas exitosamente',
            'data' => [
                'period' => "Últimos {$days} días",
                'stats' => $dailyStats,
                'total_in_period' => $dailyStats->sum('users_count')
            ]
        ], 200);
    }

    /**
     * Obtener estadísticas de usuarios registrados por semana
     */
    public function weeklyRegistrations(Request $request)
    {
        $weeks = $request->get('weeks', 12); // Por defecto últimas 12 semanas
        
        $weeklyStats = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('WEEK(created_at) as week'),
                DB::raw('COUNT(*) as users_count'),
                DB::raw('MIN(DATE(created_at)) as week_start'),
                DB::raw('MAX(DATE(created_at)) as week_end')
            )
            ->where('created_at', '>=', Carbon::now()->subWeeks($weeks))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('WEEK(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas semanales obtenidas exitosamente',
            'data' => [
                'period' => "Últimas {$weeks} semanas",
                'stats' => $weeklyStats,
                'total_in_period' => $weeklyStats->sum('users_count')
            ]
        ], 200);
    }

    /**
     * Obtener estadísticas de usuarios registrados por mes
     */
    public function monthlyRegistrations(Request $request)
    {
        $months = $request->get('months', 12); // Por defecto últimos 12 meses
        
        $monthlyStats = User::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as users_count'),
                DB::raw('MONTHNAME(created_at) as month_name')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths($months))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas mensuales obtenidas exitosamente',
            'data' => [
                'period' => "Últimos {$months} meses",
                'stats' => $monthlyStats,
                'total_in_period' => $monthlyStats->sum('users_count')
            ]
        ], 200);
    }

    /**
     * Obtener estadísticas demográficas de usuarios
     */
    public function demographics()
    {
        $genderStats = User::select('gender', DB::raw('COUNT(*) as count'))
                          ->whereNotNull('gender')
                          ->groupBy('gender')
                          ->get();

        $ageGroups = User::select(
                DB::raw('CASE 
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) < 18 THEN "Menor de 18"
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) BETWEEN 18 AND 25 THEN "18-25"
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) BETWEEN 26 AND 35 THEN "26-35"
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) BETWEEN 36 AND 45 THEN "36-45"
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) BETWEEN 46 AND 55 THEN "46-55"
                    WHEN YEAR(CURDATE()) - YEAR(birth_date) > 55 THEN "Mayor de 55"
                    ELSE "No especificado"
                END as age_group'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('age_group')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Estadísticas demográficas obtenidas exitosamente',
            'data' => [
                'gender_distribution' => $genderStats,
                'age_distribution' => $ageGroups,
                'total_users_with_gender' => $genderStats->sum('count'),
                'total_users_with_age' => User::whereNotNull('birth_date')->count()
            ]
        ], 200);
    }

    /**
     * Obtener usuarios más activos
     */
    public function mostActiveUsers(Request $request)
    {
        $limit = $request->get('limit', 10);

        $activeUsers = User::withCount(['products', 'ratings', 'comments'])
                          ->orderByDesc('products_count')
                          ->orderByDesc('ratings_count')
                          ->orderByDesc('comments_count')
                          ->limit($limit)
                          ->get();

        return response()->json([
            'success' => true,
            'message' => 'Usuarios más activos obtenidos exitosamente',
            'data' => $activeUsers
        ], 200);
    }
}
