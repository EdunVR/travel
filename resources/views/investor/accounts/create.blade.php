<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun Investasi - Portal Investor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar (sama seperti sebelumnya) -->
        @include('investor.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Tambah Akun Investasi</h1>
                        <p class="text-gray-500">Tambahkan akun bank baru untuk investasi</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 rounded-full hover:bg-gray-100">
                            <i class="fas fa-bell text-gray-600"></i>
                        </button>
                        <form method="POST" action="{{ route('investor.logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-green-700">
                                <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
                    <form action="{{ route('investor.investments.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Informasi Bank -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Bank</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="bank_name" class="block text-sm font-medium text-gray-700">Nama Bank</label>
                                        <select id="bank_name" name="bank_name" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                                            <option value="">Pilih Bank</option>
                                            <option value="BCA">BCA</option>
                                            <option value="BRI">BRI</option>
                                            <option value="Mandiri">Mandiri</option>
                                            <option value="BNI">BNI</option>
                                            <option value="BSI">BSI</option>
                                            <option value="Bank Syariah Lainnya">Bank Syariah Lainnya</option>
                                        </select>
                                        @error('bank_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="account_number" class="block text-sm font-medium text-gray-700">Nomor Rekening</label>
                                        <input type="text" id="account_number" name="account_number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        @error('account_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="account_name" class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
                                        <input type="text" id="account_name" name="account_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        @error('account_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informasi Investasi -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Investasi</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="initial_balance" class="block text-sm font-medium text-gray-700">Saldo Awal (Rp)</label>
                                        <input type="number" id="initial_balance" name="initial_balance" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        @error('initial_balance')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="profit_percentage" class="block text-sm font-medium text-gray-700">Persentase Bagi Hasil (%)</label>
                                        <input type="number" id="profit_percentage" name="profit_percentage" min="0" max="100" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        @error('profit_percentage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tombol Submit -->
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('investor.investments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Batal
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-save mr-2"></i> Simpan Akun
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
