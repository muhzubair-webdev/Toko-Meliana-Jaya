<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlert extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:check-low';

    /**
     * The console command description.
     */
    protected $description = 'Check for low stock products and send email alerts to admin users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lowStockProducts = Product::with('category')
            ->where('min_stock', '>', 0)
            ->withCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }])
            ->get()
            ->filter(fn($p) => $p->available_count <= $p->min_stock);

        if ($lowStockProducts->isEmpty()) {
            $this->info('✅ Semua stok aman. Tidak ada peringatan.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$lowStockProducts->count()} produk dengan stok menipis:");
        foreach ($lowStockProducts as $product) {
            $this->line("  - {$product->product_name}: {$product->available_count}/{$product->min_stock} {$product->unit}");
        }

        $admins = User::where('role', User::ROLE_ADMIN)->get();

        if ($admins->isEmpty()) {
            $this->error('Tidak ada admin ditemukan untuk menerima notifikasi.');
            return self::FAILURE;
        }

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new LowStockAlert($lowStockProducts));
            $this->info("📧 Email terkirim ke: {$admin->name} ({$admin->email})");
        }

        $this->info('Selesai!');
        return self::SUCCESS;
    }
}
