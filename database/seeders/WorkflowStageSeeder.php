<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkflowStage;

class WorkflowStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'stage_code' => 'product_analysis',
                'stage_name' => 'Analisis Produk',
                'stage_order' => 1,
                'description' => 'Tahap awal untuk merancang dan menghitung biaya paket perjalanan. Hitung HPP dan tentukan kelayakan paket.',
                'responsible_team' => 'finance',
                'is_active' => true
            ],
            [
                'stage_code' => 'flight_tickets',
                'stage_name' => 'Tiket Pesawat',
                'stage_order' => 2,
                'description' => 'Booking dan konfirmasi tiket pesawat untuk paket perjalanan. Pastikan semua kursi sudah dipesan.',
                'responsible_team' => 'administration',
                'is_active' => true
            ],
            [
                'stage_code' => 'design_materials',
                'stage_name' => 'Materi Desain',
                'stage_order' => 3,
                'description' => 'Buat materi marketing termasuk flyer, itinerary, video promosi, dan informasi paket.',
                'responsible_team' => 'media',
                'is_active' => true
            ],
            [
                'stage_code' => 'finance',
                'stage_name' => 'Keuangan',
                'stage_order' => 4,
                'description' => 'Buat invoice dan atur sistem pembayaran untuk pelanggan.',
                'responsible_team' => 'finance',
                'is_active' => true
            ],
            [
                'stage_code' => 'follow_up',
                'stage_name' => 'Follow-up',
                'stage_order' => 5,
                'description' => 'Hubungi calon pelanggan dan lacak upaya komunikasi serta respons.',
                'responsible_team' => 'customer_service',
                'is_active' => true
            ],
            [
                'stage_code' => 'closing',
                'stage_name' => 'Closing',
                'stage_order' => 6,
                'description' => 'Finalisasi kesepakatan dengan konfirmasi komitmen pelanggan.',
                'responsible_team' => 'customer_service',
                'is_active' => true
            ],
            [
                'stage_code' => 'cs_all_divisions',
                'stage_name' => 'CS/Semua Divisi',
                'stage_order' => 7,
                'description' => 'Koordinasi layanan pelanggan di semua divisi untuk dukungan komprehensif.',
                'responsible_team' => 'customer_service',
                'is_active' => true
            ],
            [
                'stage_code' => 'social_media',
                'stage_name' => 'Media Sosial',
                'stage_order' => 8,
                'description' => 'Kelola promosi media sosial dan pengumpulan testimoni.',
                'responsible_team' => 'media',
                'is_active' => true
            ],
            [
                'stage_code' => 'administration',
                'stage_name' => 'Administrasi',
                'stage_order' => 9,
                'description' => 'Proses semua dokumen yang diperlukan termasuk paspor, visa, tiket, asuransi, dan sertifikat kesehatan.',
                'responsible_team' => 'administration',
                'is_active' => true
            ],
            [
                'stage_code' => 'logistics',
                'stage_name' => 'Logistik',
                'stage_order' => 10,
                'description' => 'Kelola persiapan peralatan dan pengiriman untuk grup keberangkatan.',
                'responsible_team' => 'logistics',
                'is_active' => true
            ],
            [
                'stage_code' => 'save_jamaah_data',
                'stage_name' => 'Simpan Data Jamaah',
                'stage_order' => 11,
                'description' => 'Validasi dan simpan data lengkap jamaah sebelum keberangkatan.',
                'responsible_team' => 'administration',
                'is_active' => true
            ],
            [
                'stage_code' => 'offer_package',
                'stage_name' => 'Offer Package',
                'stage_order' => 12,
                'description' => 'Present final package offering to customers with all details confirmed.',
                'responsible_team' => 'customer_service',
                'is_active' => true
            ]
        ];

        foreach ($stages as $stage) {
            WorkflowStage::updateOrCreate(
                ['stage_code' => $stage['stage_code']],
                $stage
            );
        }
    }
}
