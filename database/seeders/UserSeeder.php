<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $users = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1234567890',
                'birth_date' => '1990-05-15',
                'gender' => 'male',
                'bio' => 'Desarrollador apasionado por la tecnología',
                'avatar' => 'https://via.placeholder.com/150/0000FF/FFFFFF?text=JP',
                'is_active' => true,
            ],
            [
                'name' => 'María García',
                'email' => 'maria@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+0987654321',
                'birth_date' => '1985-08-22',
                'gender' => 'female',
                'bio' => 'Diseñadora UX/UI con experiencia en e-commerce',
                'avatar' => 'https://via.placeholder.com/150/FF0000/FFFFFF?text=MG',
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+1122334455',
                'birth_date' => '1992-12-03',
                'gender' => 'male',
                'bio' => 'Emprendedor en el sector tecnológico',
                'avatar' => 'https://via.placeholder.com/150/00FF00/FFFFFF?text=CR',
                'is_active' => true,
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+5566778899',
                'birth_date' => '1988-03-18',
                'gender' => 'female',
                'bio' => 'Marketing digital y social media',
                'avatar' => 'https://via.placeholder.com/150/FFFF00/000000?text=AL',
                'is_active' => true,
            ],
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('demo123'),
                'phone' => '+9999999999',
                'birth_date' => '1995-01-01',
                'gender' => 'other',
                'bio' => 'Usuario de demostración para testing',
                'avatar' => 'https://via.placeholder.com/150/FF00FF/FFFFFF?text=DU',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Crear usuarios adicionales con fechas distribuidas en el tiempo para estadísticas
        $additionalUsers = [];
        $startDate = now()->subMonths(6);
        
        for ($i = 1; $i <= 50; $i++) {
            $randomDate = $startDate->copy()->addDays(rand(0, 180));
            
            $additionalUsers[] = [
                'name' => "Usuario Test {$i}",
                'email' => "test{$i}@example.com",
                'password' => Hash::make('password123'),
                'phone' => '+' . str_pad(rand(1000000000, 9999999999), 10, '0'),
                'birth_date' => now()->subYears(rand(18, 65))->format('Y-m-d'),
                'gender' => ['male', 'female', 'other'][rand(0, 2)],
                'bio' => "Biografía del usuario de prueba número {$i}",
                'avatar' => "https://via.placeholder.com/150/" . sprintf('%06X', mt_rand(0, 0xFFFFFF)) . "/FFFFFF?text=U{$i}",
                'is_active' => rand(0, 1) == 1,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ];
        }

        foreach ($additionalUsers as $userData) {
            User::create($userData);
        }
    }
}
