@extends('investor.layouts.app')

@section('title', 'Profil Investor - Portal Investor')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    </style>
@endpush

@section('content')
    <main class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">Profil Saya</h2>
                <p class="text-gray-500">Kelola informasi profil dan akun Anda</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Profil -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500 lg:col-span-2">
                <h3 class="text-lg font-semibold mb-4">Informasi Profil</h3>
                <form action="{{ route('investor.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ auth()->guard('investor')->user()->name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" value="{{ auth()->guard('investor')->user()->email }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" disabled>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ auth()->guard('investor')->user()->phone }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ auth()->guard('investor')->user()->birth_date }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                        <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">{{ auth()->guard('investor')->user()->address }}</textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Foto Profil & Keamanan -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold mb-4">Foto Profil</h3>
                    <div class="flex flex-col items-center">
                        <div class="relative mb-4">
                            <img src="{{ auth()->guard('investor')->user()->photoUrl }}" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow">
                            <label for="photo-upload" class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600">
                                <i class="fas fa-camera"></i>
                                <input id="photo-upload" type="file" name="photo" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 text-center">Format: JPG, PNG (Maks. 2MB)</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <h3 class="text-lg font-semibold mb-4">Keamanan Akun</h3>
                    <!-- <a href="{{ route('investor.password.change') }}" class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-md hover:bg-purple-700 transition-colors mb-3">
                        <i class="fas fa-key mr-2"></i> Ubah Password
                    </a> -->
                    <form method="POST" action="{{ route('investor.logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.getElementById('photo-upload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.querySelector('.rounded-full img').src = event.target.result;
                    
                    // Auto submit form when photo is selected
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('investor.profile.update-photo') }}";
                    form.enctype = 'multipart/form-data';
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';
                    form.appendChild(method);
                    
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'photo';
                    input.files = e.target.files;
                    form.appendChild(input);
                    
                    document.body.appendChild(form);
                    form.submit();
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
@endpush
