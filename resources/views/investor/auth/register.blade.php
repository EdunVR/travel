@extends('investor.layouts.auth')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-16 w-auto" src="{{ asset('img/logo.png') }}" alt="Logo">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Pendaftaran Investor Baru
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-3xl">
        <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
            <form class="mb-0 space-y-6" action="{{ route('investor.register') }}" method="POST" id="registrationForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom 1 -->
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input id="name" name="name" type="text" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" name="email" type="email" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                            <input id="phone" name="phone" type="text" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="space-y-4">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input id="birth_date" name="birth_date" type="date" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="nominal_invest" class="block text-sm font-medium text-gray-700">Nominal Investasi</label>
                            <input id="nominal_invest" name="nominal_invest" type="number" min="0" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3" required
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="agreement" name="agreement" type="checkbox" required
                               class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="agreement" class="font-medium text-gray-700">Saya menyetujui</label>
                        <p class="text-gray-500">Akad dan Syarat & Ketentuan yang berlaku untuk Investor</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" onclick="sendToWhatsApp()"
                            class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md shadow-sm text-green-700 bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fab fa-whatsapp mr-2"></i> Kirim via WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    function sendToWhatsApp() {
        // Validasi form
        if (!document.getElementById('agreement').checked) {
            alert('Anda harus menyetujui akad investor terlebih dahulu');
            return;
        }

        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        const nominalFormatted = formatRupiah(data.nominal_invest);

        const message = `Halo, saya ingin mendaftar sebagai investor dengan data berikut:

Nama Lengkap: ${data.name}
Email: ${data.email}
Nomor Telepon: ${data.phone}
Tanggal Lahir: ${data.birth_date}
Alamat: ${data.address}
Nominal Investasi: ${nominalFormatted}

Saya telah menyetujui akad investor. Mohon proses pendaftaran saya.`;

        const encodedMessage = encodeURIComponent(message);

        // Buka WhatsApp dengan nomor tujuan dan pesan terformat
        window.open(`https://wa.me/6289699497272?text=${encodedMessage}`, '_blank');
    }

    @if(auth()->guard('investor')->check())
        window.location.href = "{{ route('investor.dashboard') }}";
    @endif
</script>
@endsection
