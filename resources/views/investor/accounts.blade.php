<x-investor.layout title="Investasi Saya">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Akun Investasi Saya</h2>
        <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Akun
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accounts as $account)
        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 {{ $account->status === 'active' ? 'border-green-500' : 'border-gray-400' }}">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-lg">{{ $account->bank_name }}</h3>
                        <p class="text-gray-500">{{ $account->account_number }}</p>
                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full 
                              {{ $account->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $account->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="p-2 rounded-full {{ $account->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                        <i class="fas fa-university text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Saldo Awal</span>
                        <span class="font-medium">@money($account->initial_balance)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Saldo Saat Ini</span>
                        <span class="font-medium">@money($account->current_balance)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Investasi</span>
                        <span class="font-medium">@money($account->total_investment)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bagi Hasil</span>
                        <span class="font-medium">{{ number_format($account->profit_percentage, 2) }}%</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex justify-end">
                <a href="{{ route('investor.accounts.show', $account->id) }}" 
                   class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                    Lihat Detail <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-university text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-700">Belum ada akun investasi</h3>
            <p class="text-gray-500 mt-2">Mulai dengan menambahkan akun investasi Anda</p>
            <button class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Akun
            </button>
        </div>
        @endforelse
    </div>
</x-investor.layout>
