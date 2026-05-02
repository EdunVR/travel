<x-investor.layout title="Bagi Hasil">
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Distribusi Bagi Hasil</h2>
        <p class="text-gray-500">Riwayat pembagian keuntungan investasi Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Total Keuntungan</p>
                    <h3 class="text-2xl font-bold mt-1">@money($investor->total_profit)</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-600">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Bagi Hasil Investor</p>
                    <h3 class="text-2xl font-bold mt-1">@money($investor->profit_share)</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ number_format($investor->average_percentage, 2) }}% dari total</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Bagi Hasil Manajemen</p>
                    <h3 class="text-2xl font-bold mt-1">@money($investor->management_profit)</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ number_format(100 - $investor->average_percentage, 2) }}% dari total</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                    <i class="fas fa-building text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold">Riwayat Distribusi</h3>
            <div class="relative">
                <select class="appearance-none bg-gray-100 border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>Semua Periode</option>
                    @foreach($periods as $period)
                    <option value="{{ $period }}">{{ $period }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
        </div>
        <div class="divide-y">
            @forelse($profitDistributions as $distribution)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium">{{ $distribution->period }}</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            Dibagikan pada {{ $distribution->distribution_date->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="font-medium text-green-600">+@money($distribution->amount)</span>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $distribution->account->bank_name }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Total Keuntungan</p>
                        <p class="font-medium">@money($distribution->total_profit)</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Persentase</p>
                        <p class="font-medium">{{ number_format($distribution->percentage, 2) }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Akun Tujuan</p>
                        <p class="font-medium">{{ $distribution->account->account_number }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                Belum ada distribusi bagi hasil
            </div>
            @endforelse
        </div>
        @if($profitDistributions->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $profitDistributions->links() }}
        </div>
        @endif
    </div>
</x-investor.layout>
