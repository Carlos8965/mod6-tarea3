<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Rating;
use App\Models\Comment;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() === 0) {
            $this->command->error('No hay usuarios en la base de datos. Ejecuta UserSeeder primero.');
            return;
        }

        // Productos de ejemplo
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'El iPhone más avanzado con chip A17 Pro, cámara de 48MP y pantalla Super Retina XDR.',
                'price' => 999.99,
                'category' => 'Electrónicos',
                'brand' => 'Apple',
                'sku' => 'APPLE-IP15-PRO-001',
                'stock' => 50,
                'images' => ['https://via.placeholder.com/400x300/000000/FFFFFF?text=iPhone+15+Pro'],
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Smartphone premium con S Pen integrado, cámara de 200MP y pantalla Dynamic AMOLED 2X.',
                'price' => 1199.99,
                'category' => 'Electrónicos',
                'brand' => 'Samsung',
                'sku' => 'SAMSUNG-S24-ULTRA-001',
                'stock' => 30,
                'images' => ['https://via.placeholder.com/400x300/1f1f1f/FFFFFF?text=Galaxy+S24+Ultra'],
                'is_active' => true,
            ],
            [
                'name' => 'MacBook Pro 16"',
                'description' => 'Laptop profesional con chip M3 Max, 36GB RAM y pantalla Liquid Retina XDR.',
                'price' => 2499.99,
                'category' => 'Computadoras',
                'brand' => 'Apple',
                'sku' => 'APPLE-MBP-16-M3-001',
                'stock' => 15,
                'images' => ['https://via.placeholder.com/400x300/c0c0c0/000000?text=MacBook+Pro+16'],
                'is_active' => true,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Audífonos inalámbricos con cancelación de ruido líder en la industria.',
                'price' => 399.99,
                'category' => 'Audio',
                'brand' => 'Sony',
                'sku' => 'SONY-WH1000XM5-001',
                'stock' => 75,
                'images' => ['https://via.placeholder.com/400x300/000000/FFFFFF?text=Sony+WH-1000XM5'],
                'is_active' => true,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Consola híbrida con pantalla OLED de 7 pulgadas y dock mejorado.',
                'price' => 349.99,
                'category' => 'Gaming',
                'brand' => 'Nintendo',
                'sku' => 'NINTENDO-SWITCH-OLED-001',
                'stock' => 40,
                'images' => ['https://via.placeholder.com/400x300/e60012/FFFFFF?text=Nintendo+Switch+OLED'],
                'is_active' => true,
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultrabook premium con procesador Intel Core i7 de 13ª generación.',
                'price' => 1299.99,
                'category' => 'Computadoras',
                'brand' => 'Dell',
                'sku' => 'DELL-XPS13-I7-001',
                'stock' => 25,
                'images' => ['https://via.placeholder.com/400x300/0084ff/FFFFFF?text=Dell+XPS+13'],
                'is_active' => true,
            ],
            [
                'name' => 'iPad Air 5',
                'description' => 'Tablet versátil con chip M1, pantalla Liquid Retina y compatibilidad con Apple Pencil.',
                'price' => 599.99,
                'category' => 'Tablets',
                'brand' => 'Apple',
                'sku' => 'APPLE-IPAD-AIR5-001',
                'stock' => 60,
                'images' => ['https://via.placeholder.com/400x300/1d1d1f/FFFFFF?text=iPad+Air+5'],
                'is_active' => true,
            ],
            [
                'name' => 'Logitech MX Master 3S',
                'description' => 'Mouse inalámbrico avanzado para profesionales con rueda MagSpeed.',
                'price' => 99.99,
                'category' => 'Accesorios',
                'brand' => 'Logitech',
                'sku' => 'LOGITECH-MX-MASTER3S-001',
                'stock' => 100,
                'images' => ['https://via.placeholder.com/400x300/00b8d4/FFFFFF?text=MX+Master+3S'],
                'is_active' => true,
            ],
        ];

        $createdProducts = [];
        
        foreach ($products as $productData) {
            $randomUser = $users->random();
            $productData['user_id'] = $randomUser->id;
            
            $product = Product::create($productData);
            $createdProducts[] = $product;
        }

        // Crear valoraciones y comentarios aleatorios
        foreach ($createdProducts as $product) {
            // Generar entre 3 y 15 valoraciones por producto
            $ratingCount = rand(3, 15);
            $usedUsers = [$product->user_id]; // El propietario no puede valorar su propio producto
            
            for ($i = 0; $i < $ratingCount; $i++) {
                $availableUsers = $users->whereNotIn('id', $usedUsers);
                
                if ($availableUsers->count() === 0) {
                    break;
                }
                
                $randomUser = $availableUsers->random();
                $usedUsers[] = $randomUser->id;
                
                $rating = Rating::create([
                    'user_id' => $randomUser->id,
                    'product_id' => $product->id,
                    'rating' => rand(3, 5), // Valoraciones mayormente positivas
                    'review' => $this->generateRandomReview(),
                ]);

                // 70% de probabilidad de que el usuario también comente
                if (rand(1, 10) <= 7) {
                    Comment::create([
                        'user_id' => $randomUser->id,
                        'product_id' => $product->id,
                        'comment' => $this->generateRandomComment(),
                        'is_approved' => rand(1, 10) <= 9, // 90% de comentarios aprobados
                    ]);
                }
            }
        }
    }

    private function generateRandomReview(): string
    {
        $reviews = [
            'Excelente producto, cumple con todas mis expectativas.',
            'Muy buena calidad, lo recomiendo totalmente.',
            'Producto de alta calidad, vale la pena la inversión.',
            'Funciona perfectamente, muy satisfecho con la compra.',
            'Superó mis expectativas, definitivamente lo volvería a comprar.',
            'Buen producto, relación calidad-precio adecuada.',
            'Muy contento con la compra, llegó en perfectas condiciones.',
            'Producto excepcional, diseño y funcionalidad impecables.',
            'Cumple con lo prometido, muy recomendable.',
            'Calidad premium, justifica completamente el precio.',
        ];

        return $reviews[array_rand($reviews)];
    }

    private function generateRandomComment(): string
    {
        $comments = [
            '¿Viene con garantía extendida?',
            'Perfecto para uso profesional.',
            'Lo he estado usando durante semanas y funciona de maravilla.',
            '¿Está disponible en otros colores?',
            'Muy fácil de usar, interfaz intuitiva.',
            'La entrega fue rápida y el empaque excelente.',
            'Comparado con la competencia, es superior.',
            '¿Cuándo tendrán stock nuevamente?',
            'Ideal para principiantes y expertos.',
            'La relación calidad-precio es increíble.',
            'Funciones avanzadas que realmente marcan la diferencia.',
            'Diseño elegante y moderno.',
        ];

        return $comments[array_rand($comments)];
    }
}
