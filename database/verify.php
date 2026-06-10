<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATA VERIFICATION ===" . PHP_EOL . PHP_EOL;

echo "Users: " . App\Models\User::count() . PHP_EOL;
echo "Categories: " . App\Models\Category::count() . PHP_EOL;
echo "Products: " . App\Models\Product::count() . PHP_EOL;

echo PHP_EOL . "--- Users ---" . PHP_EOL;
foreach(App\Models\User::all() as $u) {
    echo "  {$u->name} | {$u->email} | {$u->role}" . PHP_EOL;
}

echo PHP_EOL . "--- Categories ---" . PHP_EOL;
foreach(App\Models\Category::all() as $c) {
    echo "  {$c->name}" . PHP_EOL;
}

echo PHP_EOL . "--- Products (with category) ---" . PHP_EOL;
foreach(App\Models\Product::with('category')->get() as $p) {
    $price = number_format($p->suggested_price, 0, ',', '.');
    echo "  {$p->product_name} | {$p->category->name} | {$p->unit} | Rp {$price}" . PHP_EOL;
}

echo PHP_EOL . "--- QR Code Generation Test ---" . PHP_EOL;
$product = App\Models\Product::with('category')->first();
$qr = App\Models\StockUnit::generateQrCode($product);
echo "  Generated QR for '{$product->product_name}': {$qr}" . PHP_EOL;

echo PHP_EOL . "=== ALL GOOD ===" . PHP_EOL;
