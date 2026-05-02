<div class="space-y-6">
    <!-- Header Info -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Informasi Pre Order</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kode:</span>
                        <span class="font-medium">{{ $preorder->kode_preorder }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal:</span>
                        <span>{{ $preorder->tanggal->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $preorder->status_badge }}">
                            {{ ucfirst($preorder->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Informasi Customer</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama:</span>
                        <span>{{ $preorder->customer->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Telepon:</span>
                        <span>{{ $preorder->customer->telepon ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span>{{ $preorder->customer->email ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div>
        <h4 class="font-medium text-gray-900 mb-3">Item Pre Order</h4>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($preorder->items as $item)
                    <tr>
                        <td class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-900">{{ $item->deskripsi }}</div>
                            @if($item->product)
                            <div class="text-xs text-gray-500">{{ $item->product->nama_produk }}</div>
                            @endif
                            
                            <!-- Additional Costs Display -->
                            @if($item->material_instalasi_biaya > 0 || $item->pemasangan_pelatihan_biaya > 0 || $item->ongkos_kirim_biaya > 0)
                            <div class="mt-2 space-y-1">
                                <div class="text-xs font-medium text-blue-600">Biaya Tambahan:</div>
                                
                                @if($item->material_instalasi_biaya > 0)
                                <div class="text-xs text-gray-600 pl-2">
                                    • Material Instalasi: Rp {{ number_format($item->material_instalasi_biaya, 0, ',', '.') }} / {{ $item->material_instalasi_satuan }}
                                    @if($item->material_instalasi_keterangan)
                                    <br><span class="text-gray-500 italic">{{ $item->material_instalasi_keterangan }}</span>
                                    @endif
                                </div>
                                @endif
                                
                                @if($item->pemasangan_pelatihan_biaya > 0)
                                <div class="text-xs text-gray-600 pl-2">
                                    • Pemasangan & Pelatihan: Rp {{ number_format($item->pemasangan_pelatihan_biaya, 0, ',', '.') }} / {{ $item->pemasangan_pelatihan_satuan }}
                                    @if($item->pemasangan_pelatihan_keterangan)
                                    <br><span class="text-gray-500 italic">{{ $item->pemasangan_pelatihan_keterangan }}</span>
                                    @endif
                                </div>
                                @endif
                                
                                @if($item->ongkos_kirim_biaya > 0)
                                <div class="text-xs text-gray-600 pl-2">
                                    • Ongkos Kirim: Rp {{ number_format($item->ongkos_kirim_biaya, 0, ',', '.') }} / {{ $item->ongkos_kirim_satuan }}
                                    @if($item->ongkos_kirim_komponen && count($item->ongkos_kirim_komponen) > 0)
                                    <br><span class="text-gray-500">Komponen: 
                                        @foreach($item->formatted_ongkos_kirim_komponen as $komponen)
                                        {{ $komponen['nama'] }} ({{ $komponen['formatted_biaya'] }}){{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </span>
                                    @endif
                                </div>
                                @endif
                                
                                <div class="text-xs font-medium text-blue-700 pl-2">
                                    Total Biaya Tambahan: Rp {{ number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.') }}
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $item->qty }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            @if($item->calculateTotalBiayaTambahan() > 0)
                            <div class="text-xs text-blue-600">+ Rp {{ number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.') }}</div>
                            <div class="text-xs font-medium text-green-600">= Rp {{ number_format($item->total_with_additional_costs, 0, ',', '.') }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal Produk:</span>
                <span>Rp {{ number_format($preorder->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($preorder->total_additional_costs > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Total Biaya Tambahan:</span>
                <span class="text-blue-600">Rp {{ number_format($preorder->total_additional_costs, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-medium">
                <span class="text-gray-600">Subtotal:</span>
                <span>Rp {{ number_format($preorder->subtotal_with_additional_costs, 0, ',', '.') }}</span>
            </div>
            @else
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal:</span>
                <span>Rp {{ number_format($preorder->subtotal, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($preorder->diskon > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Diskon:</span>
                <span class="text-red-600">- Rp {{ number_format($preorder->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($preorder->pajak > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Pajak:</span>
                <span>Rp {{ number_format($preorder->pajak, 0, ',', '.') }}</span>
            </div>
            @endif
            <hr class="border-gray-300">
            <div class="flex justify-between font-medium">
                <span>Total:</span>
                <span class="text-lg">Rp {{ number_format($preorder->grand_total_with_additional_costs, 0, ',', '.') }}</span>
            </div>
            @if($preorder->dp_amount > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">DP Dibayar:</span>
                <span class="text-green-600">Rp {{ number_format($preorder->dp_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-medium">
                <span class="text-gray-600">Sisa Pembayaran:</span>
                <span class="text-orange-600">Rp {{ number_format($preorder->grand_total_with_additional_costs - $preorder->dp_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes -->
    @if($preorder->catatan)
    <div>
        <h4 class="font-medium text-gray-900 mb-2">Catatan</h4>
        <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700">
            {{ $preorder->catatan }}
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex gap-3 pt-4 border-t border-gray-200">
        @if($preorder->status === 'penawaran')
        <button onclick="updateStatus({{ $preorder->id }}, 'invoice')" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            Ubah ke Invoice
        </button>
        @elseif($preorder->status === 'invoice')
        <button onclick="updateStatus({{ $preorder->id }}, 'lunas')" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
            Tandai Lunas
        </button>
        @endif
        
        <div class="flex gap-2">
            <button onclick="printDocument('penawaran', {{ $preorder->id }})" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Penawaran
            </button>
            @if($preorder->status !== 'penawaran')
            <button onclick="printDocument('invoice', {{ $preorder->id }})" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Invoice
            </button>
            @endif
            @if($preorder->status === 'lunas')
            <button onclick="printDocument('kwitansi', {{ $preorder->id }})" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                Print Kwitansi
            </button>
            @endif
        </div>
    </div>
</div>

<script>
function printDocument(type, preOrderId) {
    const url = `/admin/penjualan/preorders/${preOrderId}/print/${type}`;
    window.open(url, '_blank');
}
</script>