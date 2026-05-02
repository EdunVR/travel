<?php

/**
 * Travel Management System - Validation Messages (Indonesian)
 * User-friendly error messages for all validation rules
 */

return [
    // General validation messages
    'required' => ':attribute harus diisi.',
    'required_if' => ':attribute harus diisi ketika :other adalah :value.',
    'required_with' => ':attribute harus diisi ketika :values ada.',
    'required_without' => ':attribute harus diisi ketika :values tidak ada.',
    'numeric' => ':attribute harus berupa angka.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'positive' => ':attribute harus berupa angka positif.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
        'string' => ':attribute harus antara :min dan :max karakter.',
    ],
    'date' => ':attribute harus berupa tanggal yang valid.',
    'date_format' => ':attribute harus dalam format :format.',
    'after' => ':attribute harus setelah :date.',
    'after_or_equal' => ':attribute harus setelah atau sama dengan :date.',
    'before' => ':attribute harus sebelum :date.',
    'before_or_equal' => ':attribute harus sebelum atau sama dengan :date.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'regex' => 'Format :attribute tidak valid.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'url' => 'Format URL :attribute tidak valid.',
    'image' => ':attribute harus berupa gambar.',
    'mimes' => ':attribute harus berupa file dengan tipe: :values.',
    'max_file' => 'Ukuran :attribute tidak boleh lebih dari :max kilobytes.',

    // Travel-specific validation messages
    'departure_date' => [
        'future' => 'Tanggal keberangkatan harus di masa depan.',
        'required' => 'Tanggal keberangkatan harus diisi.',
        'invalid' => 'Tanggal keberangkatan tidak valid.',
    ],

    'return_date' => [
        'after_departure' => 'Tanggal kepulangan harus setelah tanggal keberangkatan.',
        'required' => 'Tanggal kepulangan harus diisi.',
    ],

    'jamaah_age' => [
        'umrah_minimum' => 'Jamaah harus berusia minimal 12 tahun untuk Umrah. Usia saat ini: :age tahun.',
        'hajj_minimum' => 'Jamaah harus berusia minimal 18 tahun untuk Hajj. Usia saat ini: :age tahun.',
        'birth_date_required' => 'Tanggal lahir jamaah harus diisi untuk validasi usia.',
    ],

    'mahram' => [
        'required' => 'Jamaah wanita di bawah 45 tahun harus memiliki mahram terdaftar. Usia: :age tahun.',
        'name_required' => 'Nama mahram harus diisi.',
        'relationship_required' => 'Hubungan mahram harus diisi.',
        'phone_required' => 'Nomor telepon mahram harus diisi.',
    ],

    'passport' => [
        'expiry_required' => 'Tanggal kadaluarsa passport harus diisi.',
        'expiry_too_soon' => 'Passport harus berlaku minimal 6 bulan dari tanggal keberangkatan. Kadaluarsa: :expiry_date, Minimal: :required_date.',
        'number_required' => 'Nomor passport harus diisi.',
        'number_format' => 'Format nomor passport tidak valid.',
    ],

    'ktp' => [
        'nik_required' => 'NIK KTP harus diisi.',
        'nik_format' => 'NIK KTP harus terdiri dari 16 digit angka. Panjang saat ini: :length digit.',
        'nik_invalid' => 'NIK KTP tidak valid.',
    ],

    'pricing' => [
        'below_hpp' => 'Peringatan: Harga jual (Rp :price) lebih rendah atau sama dengan HPP (Rp :hpp). Margin keuntungan: :margin%.',
        'negative_margin' => 'Harga jual tidak boleh lebih rendah dari HPP.',
        'hpp_required' => 'HPP harus dihitung terlebih dahulu sebelum menentukan harga jual.',
    ],

    'payment' => [
        'amount_required' => 'Jumlah pembayaran harus diisi.',
        'amount_positive' => 'Jumlah pembayaran harus lebih dari 0.',
        'exceeds_balance' => 'Jumlah pembayaran (Rp :amount) melebihi sisa tagihan (Rp :balance).',
        'invalid_method' => 'Metode pembayaran tidak valid.',
        'incomplete_for_departure' => 'Pembayaran belum lunas. Sisa tagihan: Rp :balance. Jamaah tidak dapat berangkat.',
    ],

    'capacity' => [
        'flight_exceeded' => 'Tidak dapat memesan :requested kursi. Hanya :available kursi tersedia dari total :total kursi.',
        'hotel_exceeded' => 'Tidak dapat memesan :requested kamar. Hanya :available kamar tersedia untuk tanggal yang dipilih.',
        'package_full' => 'Paket sudah penuh. Kapasitas: :capacity, Terisi: :booked.',
        'insufficient' => 'Kapasitas tidak mencukupi.',
    ],

    'deletion' => [
        'package_has_bookings' => 'Tidak dapat menghapus paket: terdapat :count booking aktif.',
        'flight_has_bookings' => 'Tidak dapat menghapus penerbangan: terdapat :count booking aktif.',
        'hotel_has_bookings' => 'Tidak dapat menghapus hotel: terdapat :count booking aktif.',
        'keberangkatan_has_jamaah' => 'Tidak dapat menghapus keberangkatan: terdapat :count jamaah yang sudah dikonfirmasi.',
        'has_dependencies' => 'Tidak dapat menghapus: terdapat data terkait yang masih aktif.',
    ],

    'documents' => [
        'incomplete' => 'Tidak semua jamaah memiliki dokumen lengkap yang disetujui.',
        'type_required' => 'Jenis dokumen harus dipilih.',
        'file_required' => 'File dokumen harus diunggah.',
        'file_too_large' => 'Ukuran file terlalu besar. Maksimal: :max MB.',
        'invalid_format' => 'Format file tidak didukung. Format yang diterima: :formats.',
        'expiry_warning' => 'Dokumen akan kadaluarsa dalam :days hari.',
    ],

    'workflow' => [
        'requirements_not_met' => 'Persyaratan tahap saat ini belum terpenuhi. Silakan lengkapi semua tugas terlebih dahulu.',
        'invalid_transition' => 'Transisi tahap tidak valid.',
        'stage_locked' => 'Tahap ini sudah terkunci dan tidak dapat diubah.',
    ],

    'booking' => [
        'jamaah_required' => 'Jamaah harus dipilih.',
        'package_required' => 'Paket harus dipilih.',
        'duplicate' => 'Jamaah sudah memiliki booking aktif untuk paket ini.',
        'cancelled' => 'Booking sudah dibatalkan dan tidak dapat diubah.',
    ],

    'hpp' => [
        'component_negative' => 'Komponen biaya :component tidak boleh negatif.',
        'locked' => 'HPP sudah terkunci dan tidak dapat diubah.',
        'calculation_error' => 'Terjadi kesalahan dalam perhitungan HPP.',
    ],

    'rab' => [
        'variance_warning' => 'Peringatan: Realisasi melebihi anggaran sebesar :percentage% untuk item :item.',
        'budget_exceeded' => 'Realisasi (Rp :actual) melebihi anggaran (Rp :budget) untuk item :item.',
    ],

    'equipment' => [
        'quantity_required' => 'Jumlah peralatan harus diisi.',
        'quantity_positive' => 'Jumlah peralatan harus lebih dari 0.',
        'status_invalid' => 'Status peralatan tidak valid.',
    ],

    'communication' => [
        'method_required' => 'Metode komunikasi harus dipilih.',
        'notes_required' => 'Catatan komunikasi harus diisi.',
        'date_required' => 'Tanggal komunikasi harus diisi.',
    ],

    // Field attribute names (for :attribute placeholder)
    'attributes' => [
        'package_name' => 'Nama paket',
        'package_type' => 'Jenis paket',
        'package_code' => 'Kode paket',
        'departure_date' => 'Tanggal keberangkatan',
        'return_date' => 'Tanggal kepulangan',
        'duration_days' => 'Durasi (hari)',
        'capacity' => 'Kapasitas',
        'price' => 'Harga',
        'hpp' => 'HPP',
        'profit_margin' => 'Margin keuntungan',
        'description' => 'Deskripsi',
        
        'nama' => 'Nama',
        'email' => 'Email',
        'phone' => 'Nomor telepon',
        'telepon' => 'Nomor telepon',
        'hp' => 'Nomor HP',
        'address' => 'Alamat',
        'alamat' => 'Alamat',
        
        'ktp_nik' => 'NIK KTP',
        'ktp_nama' => 'Nama KTP',
        'ktp_tanggal_lahir' => 'Tanggal lahir',
        'ktp_alamat' => 'Alamat KTP',
        
        'passport_nomor' => 'Nomor passport',
        'passport_nama' => 'Nama passport',
        'passport_tanggal_terbit' => 'Tanggal terbit passport',
        'passport_tanggal_kadaluarsa' => 'Tanggal kadaluarsa passport',
        
        'mahram_name' => 'Nama mahram',
        'mahram_relationship' => 'Hubungan mahram',
        'mahram_phone' => 'Telepon mahram',
        
        'health_conditions' => 'Kondisi kesehatan',
        'emergency_contact_name' => 'Nama kontak darurat',
        'emergency_contact_phone' => 'Telepon kontak darurat',
        'room_preference' => 'Preferensi kamar',
        'special_requests' => 'Permintaan khusus',
        
        'flight_number' => 'Nomor penerbangan',
        'airline_name' => 'Nama maskapai',
        'departure_airport' => 'Bandara keberangkatan',
        'arrival_airport' => 'Bandara tujuan',
        'departure_time' => 'Waktu keberangkatan',
        'arrival_time' => 'Waktu tiba',
        
        'hotel_name' => 'Nama hotel',
        'location' => 'Lokasi',
        'city' => 'Kota',
        'country' => 'Negara',
        'star_rating' => 'Rating bintang',
        'total_rooms' => 'Jumlah kamar',
        
        'payment_amount' => 'Jumlah pembayaran',
        'payment_method' => 'Metode pembayaran',
        'payment_date' => 'Tanggal pembayaran',
        
        'document_type' => 'Jenis dokumen',
        'document_number' => 'Nomor dokumen',
        'issue_date' => 'Tanggal terbit',
        'expiry_date' => 'Tanggal kadaluarsa',
        
        'equipment_name' => 'Nama peralatan',
        'quantity' => 'Jumlah',
        'status' => 'Status',
        
        'communication_method' => 'Metode komunikasi',
        'communication_date' => 'Tanggal komunikasi',
        'notes' => 'Catatan',
    ],
];
