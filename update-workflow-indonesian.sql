-- Update Workflow Stages ke Bahasa Indonesia
UPDATE workflow_stages SET 
    stage_name = 'Analisis Produk',
    description = 'Tahap awal untuk merancang dan menghitung biaya paket perjalanan. Hitung HPP dan tentukan kelayakan paket.'
WHERE stage_code = 'product_analysis';

UPDATE workflow_stages SET 
    stage_name = 'Tiket Pesawat',
    description = 'Booking dan konfirmasi tiket pesawat untuk paket perjalanan. Pastikan semua kursi sudah dipesan.'
WHERE stage_code = 'flight_tickets';

UPDATE workflow_stages SET 
    stage_name = 'Materi Desain',
    description = 'Buat materi marketing termasuk flyer, itinerary, video promosi, dan informasi paket.'
WHERE stage_code = 'design_materials';

UPDATE workflow_stages SET 
    stage_name = 'Keuangan',
    description = 'Buat invoice dan atur sistem pembayaran untuk pelanggan.'
WHERE stage_code = 'finance';

UPDATE workflow_stages SET 
    stage_name = 'Follow-up',
    description = 'Hubungi calon pelanggan dan lacak upaya komunikasi serta respons.'
WHERE stage_code = 'follow_up';

UPDATE workflow_stages SET 
    stage_name = 'Closing',
    description = 'Finalisasi kesepakatan dengan konfirmasi komitmen pelanggan.'
WHERE stage_code = 'closing';

UPDATE workflow_stages SET 
    stage_name = 'CS/Semua Divisi',
    description = 'Koordinasi layanan pelanggan di semua divisi untuk dukungan komprehensif.'
WHERE stage_code = 'cs_all_divisions';

UPDATE workflow_stages SET 
    stage_name = 'Media Sosial',
    description = 'Kelola promosi media sosial dan pengumpulan testimoni.'
WHERE stage_code = 'social_media';

UPDATE workflow_stages SET 
    stage_name = 'Administrasi',
    description = 'Proses semua dokumen yang diperlukan termasuk paspor, visa, tiket, asuransi, dan sertifikat kesehatan.'
WHERE stage_code = 'administration';

UPDATE workflow_stages SET 
    stage_name = 'Logistik',
    description = 'Kelola persiapan peralatan dan pengiriman untuk grup keberangkatan.'
WHERE stage_code = 'logistics';

UPDATE workflow_stages SET 
    stage_name = 'Simpan Data Jamaah',
    description = 'Validasi dan simpan data lengkap jamaah sebelum keberangkatan.'
WHERE stage_code = 'save_jamaah_data';

-- Update Workflow Tasks ke Bahasa Indonesia untuk stage product_analysis
UPDATE workflow_tasks SET 
    task_name = 'Hitung HPP',
    task_description = 'Hitung Harga Pokok Penjualan untuk paket termasuk semua biaya (penerbangan, hotel, transportasi, makan, visa, guide, asuransi, overhead operasional, dan kontingensi)'
WHERE task_name = 'Calculate HPP';

UPDATE workflow_tasks SET 
    task_name = 'Tentukan Harga Jual',
    task_description = 'Tetapkan harga jual berdasarkan HPP dan margin keuntungan yang diinginkan'
WHERE task_name = 'Determine Selling Price';

UPDATE workflow_tasks SET 
    task_name = 'Verifikasi Kelayakan Paket',
    task_description = 'Pastikan paket layak secara finansial dan kompetitif di pasar'
WHERE task_name = 'Verify Package Viability';

-- Update Workflow Tasks untuk stage flight_tickets
UPDATE workflow_tasks SET 
    task_name = 'Booking Tiket Pesawat',
    task_description = 'Pesan tiket pesawat untuk semua jamaah sesuai kapasitas paket'
WHERE task_name = 'Book Flight Tickets';

UPDATE workflow_tasks SET 
    task_name = 'Konfirmasi Reservasi',
    task_description = 'Dapatkan konfirmasi dari maskapai untuk semua booking'
WHERE task_name = 'Confirm Reservations';

UPDATE workflow_tasks SET 
    task_name = 'Update Data Penerbangan',
    task_description = 'Perbarui sistem dengan detail penerbangan dan nomor booking'
WHERE task_name = 'Update Flight Data';

-- Update Workflow Tasks untuk stage design_materials
UPDATE workflow_tasks SET 
    task_name = 'Desain Flyer',
    task_description = 'Buat flyer promosi yang menarik untuk paket'
WHERE task_name = 'Design Flyer';

UPDATE workflow_tasks SET 
    task_name = 'Buat Itinerary',
    task_description = 'Siapkan itinerary perjalanan detail untuk jamaah'
WHERE task_name = 'Create Itinerary';

UPDATE workflow_tasks SET 
    task_name = 'Produksi Video Promosi',
    task_description = 'Buat video promosi untuk media sosial dan marketing'
WHERE task_name = 'Produce Promotional Video';

UPDATE workflow_tasks SET 
    task_name = 'Kompilasi Informasi Paket',
    task_description = 'Kumpulkan semua informasi paket dalam format yang mudah dibagikan'
WHERE task_name = 'Compile Package Information';
