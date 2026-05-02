<?php
/**
 * Debug script untuk masalah due_date pada invoice
 */

echo "=== DEBUG INVOICE DUE_DATE ISSUE ===\n\n";

// Simulasi data yang dikirim dari frontend
$frontendData = [
    'tanggal' => '2026-02-03',
    'due_date' => null, // Kemungkinan ini yang menyebabkan error
    'customer_type' => 'member',
    'customer_id' => 164,
    'id_outlet' => '4',
    'outlet_id' => '4'
];

echo "ANALISIS MASALAH:\n";
echo "1. Error: due_date field is required\n";
echo "2. Frontend mengirim due_date tapi kemungkinan null/empty\n";
echo "3. Validasi Laravel: 'due_date' => 'required|date|after_or_equal:tanggal'\n\n";

echo "KEMUNGKINAN PENYEBAB:\n";
echo "1. invoiceForm.due_date tidak terinisialisasi dengan benar\n";
echo "2. Input date due_date kosong saat submit\n";
echo "3. Format tanggal tidak sesuai\n";
echo "4. JavaScript tidak mengambil nilai dari input\n\n";

echo "PERBAIKAN YANG SUDAH DILAKUKAN:\n";
echo "✓ Menambahkan due_date ke requestData\n";
echo "✓ Menambahkan validasi frontend untuk due_date\n";
echo "✓ Memastikan inisialisasi due_date dengan default 30 hari\n\n";

echo "LANGKAH DEBUGGING:\n";
echo "1. Periksa console.log requestData sebelum dikirim\n";
echo "2. Pastikan input due_date terisi\n";
echo "3. Periksa format tanggal yang dikirim\n";
echo "4. Test dengan tanggal manual\n\n";

echo "SOLUSI TAMBAHAN:\n";
echo "1. Tambahkan console.log untuk debug due_date\n";
echo "2. Pastikan default value due_date bekerja\n";
echo "3. Validasi format tanggal di frontend\n";
echo "4. Fallback jika due_date kosong\n\n";

// Simulasi perbaikan
echo "PERBAIKAN YANG DIPERLUKAN:\n";
echo "1. Tambahkan debug log di submitInvoice()\n";
echo "2. Pastikan due_date tidak null sebelum submit\n";
echo "3. Set default due_date jika kosong\n\n";

echo "TESTING:\n";
echo "1. Buka invoice form\n";
echo "2. Periksa nilai due_date di console\n";
echo "3. Pastikan input due_date terisi\n";
echo "4. Submit dan lihat requestData di network tab\n";
?>