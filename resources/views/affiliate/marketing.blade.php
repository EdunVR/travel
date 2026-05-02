@extends('affiliate.layouts.app')

@section('title', 'Marketing')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Material Marketing</h1>
        <p class="text-sm text-gray-500 mt-1">Gunakan material ini untuk promosi</p>
    </div>

    <!-- Referral Link -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-6 text-white">
        <h3 class="text-lg font-bold mb-4">
            <i class="fas fa-link mr-2"></i>Link Referral Anda
        </h3>
        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 mb-4">
            <div class="flex items-center gap-3">
                <input type="text" 
                       id="referralLink" 
                       value="{{ $affiliator->referral_link }}" 
                       readonly
                       class="flex-1 bg-transparent border-none text-white text-sm focus:outline-none">
                <button onclick="copyReferralLink()" 
                        class="bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-copy mr-1"></i> Salin
                </button>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="https://wa.me/?text={{ urlencode('Yuk umroh bareng! Daftar di: ' . $affiliator->referral_link) }}" 
               target="_blank"
               class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold text-center transition">
                <i class="fab fa-whatsapp mr-1"></i> Share WhatsApp
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($affiliator->referral_link) }}" 
               target="_blank"
               class="flex-1 bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-2 rounded-lg text-sm font-semibold text-center transition">
                <i class="fab fa-facebook mr-1"></i> Share Facebook
            </a>
        </div>
    </div>

    <!-- Available Packages -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Paket Tersedia untuk Dipromosikan</h3>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($packages as $package)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <h4 class="font-bold text-gray-900 mb-2">{{ $package->package_name }}</h4>
                <div class="text-sm text-gray-600 mb-3">
                    <div>Durasi: {{ $package->duration }} hari</div>
                    <div class="font-bold text-green-600 mt-1">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
                </div>
                <div class="flex gap-2">
                    <button onclick="copyPackageLink('{{ route('public.paket.show', $package->id) }}?ref={{ $affiliator->username }}')"
                            class="flex-1 px-3 py-2 bg-green-50 hover:bg-green-100 text-green-600 text-sm rounded-lg transition">
                        <i class="fas fa-copy mr-1"></i> Copy Link
                    </button>
                    <a href="https://wa.me/?text={{ urlencode('Paket ' . $package->package_name . ' - ' . route('public.paket.show', $package->id) . '?ref=' . $affiliator->username) }}"
                       target="_blank"
                       class="flex-1 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm rounded-lg transition text-center">
                        <i class="fab fa-whatsapp mr-1"></i> Share
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-8 text-gray-400">
                <i class="fas fa-box-open text-4xl mb-3 block"></i>
                Belum ada paket tersedia
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');
    alert('Link referral berhasil disalin!');
}

function copyPackageLink(link) {
    navigator.clipboard.writeText(link).then(function() {
        alert('Link paket berhasil disalin!');
    }).catch(function(err) {
        alert('Gagal copy link: ' + err);
    });
}
</script>
@endsection
