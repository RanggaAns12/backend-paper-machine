<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreOrder;
use Carbon\Carbon;

class PreOrderSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now();

        $preOrders = [
            [
                'po_number'            => 'PO-' . $today->format('Ymd') . '-001',
                'customer_name'        => 'PT. Indofood CBP Sukses Makmur',
                'order_date'           => $today->toDateString(),
                'target_delivery_date' => $today->copy()->addDays(7)->toDateString(),
                'grade'                => 'Testliner 150',
                'target_qty'           => 5000.00, // 5 Ton
                'notes'                => 'Tolong pastikan kualitas kertas sesuai standar.',
                'status'               => 'PENDING',
            ],
            [
                'po_number'            => 'PO-' . $today->format('Ymd') . '-002',
                'customer_name'        => 'PT. Mayora Indah Tbk',
                'order_date'           => $today->copy()->subDays(2)->toDateString(),
                'target_delivery_date' => $today->copy()->addDays(3)->toDateString(),
                'grade'                => 'Medium 110',
                'target_qty'           => 3500.00, // 3.5 Ton
                'notes'                => 'Pengiriman urgent, segera selesaikan.',
                'status'               => 'ON_PROGRESS', 
            ],
            [
                'po_number'            => 'PO-' . $today->format('Ymd') . '-003',
                'customer_name'        => 'CV. Mulia Box Packaging',
                'order_date'           => $today->copy()->subDays(1)->toDateString(),
                'target_delivery_date' => $today->copy()->addDays(14)->toDateString(),
                'grade'                => 'Kraftliner 125',
                'target_qty'           => 8000.00, // 8 Ton
                'notes'                => 'Core diameter custom 15cm (info ke winder)',
                'status'               => 'PENDING',
            ],
            [
                'po_number'            => 'PO-' . $today->format('Ymd') . '-004',
                'customer_name'        => 'PT. Karya Kertas Nusantara',
                'order_date'           => $today->copy()->subDays(5)->toDateString(),
                'target_delivery_date' => $today->copy()->subDays(1)->toDateString(),
                'grade'                => 'Kraftliner 150',
                'target_qty'           => 2000.00, 
                'notes'                => 'Sudah selesai dan siap untuk Delivery Order.',
                'status'               => 'COMPLETED',
            ],
        ];

        foreach ($preOrders as $po) {
            PreOrder::firstOrCreate(
                ['po_number' => $po['po_number']],
                $po
            );
        }

        $this->command->info('✅ PreOrderSeeder: Data Pre-Order berhasil disinkronisasi dengan struktur tabel terbaru.');
    }
}