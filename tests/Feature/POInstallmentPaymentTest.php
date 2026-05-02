<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\POPaymentHistory;
use App\Models\Hutang;
use App\Models\SettingCOAPurchase;
use App\Models\ChartOfAccount;
use App\Models\AccountingBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class POInstallmentPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $outlet;
    protected $supplier;
    protected $purchaseOrder;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create test user
        $this->user = User::factory()->create();

        // Create test outlet manually
        $this->outlet = Outlet::create([
            'nama_outlet' => 'Test Outlet',
            'alamat' => 'Test Address',
            'status' => 'active'
        ]);

        // Create test supplier manually
        $this->supplier = Supplier::create([
            'nama' => 'Test Supplier',
            'alamat' => 'Supplier Address',
            'no_telp' => '08123456789',
            'email' => 'test@supplier.com'
        ]);

        // Create test purchase order
        $this->purchaseOrder = PurchaseOrder::create([
            'no_po' => 'PO-TEST-001',
            'tanggal' => now(),
            'id_supplier' => $this->supplier->id_supplier,
            'id_outlet' => $this->outlet->id_outlet,
            'id_user' => $this->user->id,
            'subtotal' => 10000000,
            'total_diskon' => 0,
            'total' => 10000000,
            'total_dibayar' => 0,
            'sisa_pembayaran' => 10000000,
            'status' => 'pending',
            'due_date' => now()->addDays(30)
        ]);

        // Create PO items
        PurchaseOrderItem::create([
            'id_purchase_order' => $this->purchaseOrder->id_purchase_order,
            'tipe_item' => 'manual',
            'deskripsi' => 'Test Item',
            'kuantitas' => 10,
            'satuan' => 'pcs',
            'harga' => 1000000,
            'diskon' => 0,
            'subtotal' => 10000000
        ]);
    }

    /**
     * Test single payment (Requirement 7.1, 7.2)
     */
    public function test_single_payment_success(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'catatan' => 'First payment'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Pembayaran berhasil dicatat'
                 ]);

        // Verify payment history created
        $this->assertDatabaseHas('po_payment_history', [
            'id_purchase_order' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'penerima' => 'Test Supplier'
        ]);

        // Verify PO updated
        $this->purchaseOrder->refresh();
        $this->assertEquals(5000000, $this->purchaseOrder->total_dibayar);
        $this->assertEquals(5000000, $this->purchaseOrder->sisa_pembayaran);
        $this->assertEquals('partial', $this->purchaseOrder->status);
    }

    /**
     * Test multiple installment payments (Requirement 2.1, 2.2)
     */
    public function test_multiple_installment_payments(): void
    {
        $this->actingAs($this->user);

        // First payment
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 3000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $this->purchaseOrder->refresh();
        $this->assertEquals('partial', $this->purchaseOrder->status);
        $this->assertEquals(3000000, $this->purchaseOrder->total_dibayar);

        // Second payment
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 4000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $this->purchaseOrder->refresh();
        $this->assertEquals('partial', $this->purchaseOrder->status);
        $this->assertEquals(7000000, $this->purchaseOrder->total_dibayar);

        // Third payment (final)
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 3000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $this->purchaseOrder->refresh();
        $this->assertEquals('paid', $this->purchaseOrder->status);
        $this->assertEquals(10000000, $this->purchaseOrder->total_dibayar);
        $this->assertEquals(0, $this->purchaseOrder->sisa_pembayaran);

        // Verify 3 payment records
        $this->assertEquals(3, POPaymentHistory::where('id_purchase_order', $this->purchaseOrder->id_purchase_order)->count());
    }

    /**
     * Test full payment in one transaction (Requirement 2.3)
     */
    public function test_full_payment_at_once(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 10000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(200);

        $this->purchaseOrder->refresh();
        $this->assertEquals('paid', $this->purchaseOrder->status);
        $this->assertEquals(10000000, $this->purchaseOrder->total_dibayar);
        $this->assertEquals(0, $this->purchaseOrder->sisa_pembayaran);
    }

    /**
     * Test overpayment prevention (Requirement 2.5, 7.2)
     */
    public function test_overpayment_prevention(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 15000000, // More than total
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false
                 ]);

        // Verify no payment was recorded
        $this->assertEquals(0, POPaymentHistory::where('id_purchase_order', $this->purchaseOrder->id_purchase_order)->count());
    }

    /**
     * Test payment with image upload (Requirement 1.4, 7.3, 7.4, 8.2)
     */
    public function test_payment_with_image_upload(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('bukti.jpg', 2000, 2000)->size(3000);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'bukti_pembayaran' => $file
        ]);

        $response->assertStatus(200);

        // Verify file was saved
        $payment = POPaymentHistory::where('id_purchase_order', $this->purchaseOrder->id_purchase_order)->first();
        $this->assertNotNull($payment->bukti_pembayaran);
        Storage::disk('public')->assertExists($payment->bukti_pembayaran);
    }

    /**
     * Test payment with PDF upload (Requirement 7.3, 7.4)
     */
    public function test_payment_with_pdf_upload(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('bukti.pdf', 1000, 'application/pdf');

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'bukti_pembayaran' => $file
        ]);

        $response->assertStatus(200);

        $payment = POPaymentHistory::where('id_purchase_order', $this->purchaseOrder->id_purchase_order)->first();
        $this->assertNotNull($payment->bukti_pembayaran);
    }

    /**
     * Test invalid file type validation (Requirement 7.3)
     */
    public function test_invalid_file_type_rejected(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('bukti.txt', 100);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'bukti_pembayaran' => $file
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['bukti_pembayaran']);
    }

    /**
     * Test file size validation (Requirement 7.4)
     */
    public function test_file_size_validation(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('bukti.jpg', 6000); // 6MB

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'bukti_pembayaran' => $file
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['bukti_pembayaran']);
    }

    /**
     * Test payment amount validation (Requirement 7.1)
     */
    public function test_payment_amount_validation(): void
    {
        $this->actingAs($this->user);

        // Test zero amount
        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 0,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['jumlah_pembayaran']);

        // Test negative amount
        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => -1000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['jumlah_pembayaran']);
    }

    /**
     * Test get payment history (Requirement 4.1, 4.2, 4.3, 4.4)
     */
    public function test_get_payment_history(): void
    {
        $this->actingAs($this->user);

        // Create some payments
        POPaymentHistory::create([
            'id_purchase_order' => $this->purchaseOrder->id_purchase_order,
            'tanggal_pembayaran' => now(),
            'jumlah_pembayaran' => 3000000,
            'jenis_pembayaran' => 'cash',
            'penerima' => 'Supplier A',
            'catatan' => 'First payment'
        ]);

        POPaymentHistory::create([
            'id_purchase_order' => $this->purchaseOrder->id_purchase_order,
            'tanggal_pembayaran' => now()->addDays(1),
            'jumlah_pembayaran' => 2000000,
            'jenis_pembayaran' => 'transfer',
            'penerima' => 'Supplier A',
            'catatan' => 'Second payment'
        ]);

        $response = $this->getJson(route('purchase-order.payment-history', $this->purchaseOrder->id_purchase_order));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'purchase_order' => [
                             'no_po',
                             'total',
                             'total_dibayar',
                             'sisa_pembayaran',
                             'status'
                         ],
                         'payment_history' => [
                             '*' => [
                                 'id',
                                 'tanggal_pembayaran',
                                 'jumlah_pembayaran',
                                 'jenis_pembayaran',
                                 'penerima',
                                 'catatan'
                             ]
                         ]
                     ]
                 ]);

        $this->assertEquals(2, count($response->json('data.payment_history')));
    }

    /**
     * Test download bukti transfer (Requirement 4.5)
     */
    public function test_download_bukti_transfer(): void
    {
        $this->actingAs($this->user);

        // Create payment with bukti
        $file = UploadedFile::fake()->image('bukti.jpg');
        
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'transfer',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier',
            'bukti_pembayaran' => $file
        ]);

        $payment = POPaymentHistory::where('id_purchase_order', $this->purchaseOrder->id_purchase_order)->first();

        $response = $this->get(route('purchase-order.download-bukti', $payment->id_payment));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    /**
     * Test hutang integration (Requirement 5.1, 5.2, 5.3, 5.4)
     */
    public function test_hutang_integration(): void
    {
        $this->actingAs($this->user);

        // Make first payment
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 6000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        // Verify hutang created
        $hutang = Hutang::where('id_pembelian', $this->purchaseOrder->id_purchase_order)->first();
        $this->assertNotNull($hutang);
        $this->assertEquals(10000000, $hutang->jumlah_hutang);
        $this->assertEquals(6000000, $hutang->jumlah_dibayar);
        $this->assertEquals(4000000, $hutang->sisa_hutang);
        $this->assertEquals('belum_lunas', $hutang->status);

        // Make second payment to complete
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 4000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        // Verify hutang updated to lunas
        $hutang->refresh();
        $this->assertEquals(10000000, $hutang->jumlah_dibayar);
        $this->assertEquals(0, $hutang->sisa_hutang);
        $this->assertEquals('lunas', $hutang->status);
    }

    /**
     * Test status progression (Requirement 6.1, 6.2)
     */
    public function test_status_progression(): void
    {
        $this->actingAs($this->user);

        // Initial status
        $this->assertEquals('pending', $this->purchaseOrder->status);

        // After partial payment
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $this->purchaseOrder->refresh();
        $this->assertEquals('partial', $this->purchaseOrder->status);

        // After full payment
        $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $this->purchaseOrder->refresh();
        $this->assertEquals('paid', $this->purchaseOrder->status);
    }

    /**
     * Test authentication required (Requirement 8.1)
     */
    public function test_payment_requires_authentication(): void
    {
        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test required fields validation (Requirement 7.5)
     */
    public function test_required_fields_validation(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'po_id',
                     'jumlah_pembayaran',
                     'jenis_pembayaran',
                     'tanggal_pembayaran',
                     'penerima'
                 ]);
    }

    /**
     * Test invalid PO ID (Requirement 7.5)
     */
    public function test_invalid_po_id(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => 99999,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'cash',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['po_id']);
    }

    /**
     * Test payment type validation (Requirement 7.5)
     */
    public function test_payment_type_validation(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('purchase-order.payment'), [
            'po_id' => $this->purchaseOrder->id_purchase_order,
            'jumlah_pembayaran' => 5000000,
            'jenis_pembayaran' => 'invalid_type',
            'tanggal_pembayaran' => now()->format('Y-m-d'),
            'penerima' => 'Test Supplier'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['jenis_pembayaran']);
    }
}
