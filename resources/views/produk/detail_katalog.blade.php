<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->nama_produk }} | Dahana Boiler</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .product-gallery img {
            transition: all 0.3s ease;
        }
        
        .product-gallery img:hover {
            transform: scale(1.05);
        }
        /* Ensure consistent image display */
        .object-contain {
            object-fit: contain;
            object-position: center;
        }
        
        .object-cover {
            object-fit: cover;
            object-position: center;
        }
        
        /* Thumbnail hover effect */
        [onclick] {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        [onclick]:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    @include('partials.navbar')
    
    <!-- Product Detail Section -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Product Images -->
                <div class="md:w-1/2 p-6">
                    <!-- Main Image Container -->
                    <div class="mb-4 rounded-lg overflow-hidden shadow-md bg-gray-100" style="height: 400px;">
                        @if($product->images->isNotEmpty())
                            @php
                                $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                            @endphp
                            <img 
                                id="mainProductImage"
                                src="{{ asset('storage/'.$primaryImage->path) }}" 
                                alt="{{ $product->nama_produk }}" 
                                class="w-full h-full object-contain"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnail Gallery -->
                    @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <button 
                                onclick="document.getElementById('mainProductImage').src = '{{ asset('storage/'.$image->path) }}'"
                                class="border-2 rounded-lg overflow-hidden transition duration-200 {{ $image->is_primary ? 'border-indigo-500' : 'border-transparent hover:border-gray-300' }}"
                                style="height: 80px;"
                            >
                                <img 
                                    src="{{ asset('storage/'.$image->path) }}" 
                                    alt="{{ $product->nama_produk }}" 
                                    class="w-full h-full object-cover"
                                >
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="md:w-1/2 p-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->nama_produk }}</h1>
                    
                    <div class="flex items-center mb-4">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {{ $product->kategori->nama_kategori }}
                        </span>
                        <span class="ml-2 text-gray-500 text-sm">{{ $product->kode_produk }}</span>
                    </div>
                    
                    
                    <!-- Product Variants -->
                    @if($product->variants->count() > 1)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Varian Produk</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->variants as $variant)
                                    <button class="px-4 py-2 border rounded-lg hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $variant->is_default ? 'bg-indigo-100 border-indigo-300' : 'border-gray-300' }}"
                                            onclick="selectVariant({{ $variant->id }})">
                                        {{ $variant->nama_varian }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Product Specifications - Vertical Layout -->
                    <div class="border-t border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Spesifikasi Produk</h3>
                        
                        <!-- Technical Specifications -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Spesifikasi Teknis</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Merek</span>
                                    <span class="font-medium">{{ $product->merk ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Kategori</span>
                                    <span class="font-medium">{{ $product->kategori->nama_kategori ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Kode Produk</span>
                                    <span class="font-medium">{{ $product->kode_produk ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Satuan</span>
                                    <span class="font-medium">{{ $product->satuan->nama_satuan ?? '-' }}</span>
                                </div>
                                @if($product->stok_minimum > 0)
                                <div class="flex justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-600">Stok Minimum</span>
                                    <span class="font-medium">{{ $product->stok_minimum }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Full Description -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Deskripsi Lengkap</h4>
                            @if(!empty($product->spesifikasi))
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($product->spesifikasi)) !!}
                                </div>
                            @else
                                <p class="text-gray-500 italic">Tidak ada deskripsi tambahan tersedia</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-4">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                            Hubungi Kami
                        </button>
                        <button class="border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-3 px-6 rounded-lg transition duration-300">
                            Ajukan Syirkah
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Product Components (Bundling) -->
            @if($components->isNotEmpty())
                <div class="border-t border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Paket Termasuk</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($components as $component)
                            <div class="flex items-center border rounded-lg p-3">
                                @if($component->component && $component->component->images->isNotEmpty())
                                    <img src="{{ asset('storage/'.$component->component->images->first()->path) }}" 
                                        alt="{{ $component->component->nama_produk }}" 
                                        class="w-16 h-16 object-cover rounded-lg mr-4">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Related Products Section -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Produk Terkait</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
                <div class="bg-white rounded-lg overflow-hidden shadow-md transition duration-300 hover:shadow-lg">
                    <a href="{{ route('produk.detail', $related->id_produk) }}">
                        @if($related->images->isNotEmpty())
                            <img src="{{ asset('storage/'.$related->images->first()->path) }}" 
                                 alt="{{ $related->nama_produk }}" 
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="bg-gray-200 h-48 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $related->nama_produk }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ $related->kategori->nama_kategori }}</p>
                        <div class="flex justify-between items-center mt-4">
                           
                            <a href="{{ route('produk.detail', $related->id_produk) }}" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded transition duration-300">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <script>
        function selectVariant(variantId) {
            // You can implement variant selection logic here
            console.log('Selected variant:', variantId);
            // You might want to update price or other details based on the variant
        }
    </script>
</body>
</html>
