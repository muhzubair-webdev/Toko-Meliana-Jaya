<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed sample products for demonstration.
     */
    public function run(): void
    {
        $listrik = Category::where('name', 'Listrik')->first();
        $plastik = Category::where('name', 'Plastik')->first();

        $products = [
            // Kategori Listrik
            [
                'category_id' => $listrik->id,
                'product_name' => 'Kabel Eterna 2x1.5',
                'unit' => 'Roll',
                'min_stock' => 5,
                'suggested_price' => 250000,
            ],
            [
                'category_id' => $listrik->id,
                'product_name' => 'Kabel Eterna 2x2.5',
                'unit' => 'Roll',
                'min_stock' => 3,
                'suggested_price' => 380000,
            ],
            [
                'category_id' => $listrik->id,
                'product_name' => 'Saklar Broco',
                'unit' => 'Pcs',
                'min_stock' => 10,
                'suggested_price' => 15000,
            ],
            [
                'category_id' => $listrik->id,
                'product_name' => 'Stop Kontak Broco',
                'unit' => 'Pcs',
                'min_stock' => 10,
                'suggested_price' => 18000,
            ],
            [
                'category_id' => $listrik->id,
                'product_name' => 'MCB 6A Schneider',
                'unit' => 'Pcs',
                'min_stock' => 5,
                'suggested_price' => 45000,
            ],

            // Kategori Plastik
            [
                'category_id' => $plastik->id,
                'product_name' => 'Pipa Paralon 1/2"',
                'unit' => 'Batang',
                'min_stock' => 10,
                'suggested_price' => 25000,
            ],
            [
                'category_id' => $plastik->id,
                'product_name' => 'Pipa Paralon 3/4"',
                'unit' => 'Batang',
                'min_stock' => 10,
                'suggested_price' => 35000,
            ],
            [
                'category_id' => $plastik->id,
                'product_name' => 'Fitting Lampu',
                'unit' => 'Pcs',
                'min_stock' => 15,
                'suggested_price' => 8000,
            ],
            [
                'category_id' => $plastik->id,
                'product_name' => 'Knee Elbow PVC 1/2"',
                'unit' => 'Pcs',
                'min_stock' => 20,
                'suggested_price' => 3000,
            ],
            [
                'category_id' => $plastik->id,
                'product_name' => 'Lem Pipa PVC',
                'unit' => 'Kaleng',
                'min_stock' => 5,
                'suggested_price' => 12000,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['product_name' => $product['product_name']],
                $product
            );
        }
    }
}
