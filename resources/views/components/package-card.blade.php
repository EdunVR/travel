@props(['package'])

<a href="{{ url('/paket/' . $package->id) }}" class="block">
<div class="card-hover bg-white rounded-2xl overflow-hidden border border-green-100 shadow-sm group cursor-pointer hover:shadow-xl transition-all duration-300">
    <!-- Image Section with Fixed Aspect Ratio -->
    <div class="relative overflow-hidden" style="aspect-ratio: 16/9;">
        @if($package->image_path)
        @php
            $cropData = $package->thumbnail_crop_settings ?? null;
            $hasValidCrop = $cropData && is_array($cropData) && isset($cropData['width']) && $cropData['width'] > 0;
        @endphp
        
        @if($hasValidCrop)
        @php
            // Get crop data
            $cropX = $cropData['x'] ?? 0;
            $cropY = $cropData['y'] ?? 0;
            $cropW = $cropData['width'] ?? 1;
            $cropH = $cropData['height'] ?? 1;
        @endphp
        <img src="{{ asset('storage/'.$package->image_path) }}"
             alt="{{ $package->package_name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             data-crop-x="{{ $cropX }}"
             data-crop-y="{{ $cropY }}"
             data-crop-w="{{ $cropW }}"
             data-crop-h="{{ $cropH }}"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()"
             onload="(function(img){
                const cropX = parseFloat(img.dataset.cropX);
                const cropY = parseFloat(img.dataset.cropY);
                const cropW = parseFloat(img.dataset.cropW);
                const cropH = parseFloat(img.dataset.cropH);
                
                if (!cropW || !cropH || !img.naturalWidth || !img.naturalHeight) return;
                
                // Calculate crop area aspect ratio
                const cropAspect = cropW / cropH;
                // Container aspect ratio is 16:9
                const containerAspect = 16 / 9;
                
                // Calculate top-center point of crop area (not center-center)
                // This ensures the top part is visible
                const cropCenterX = cropX + (cropW / 2);
                // Use top of crop + 30% of crop height to show more of the top
                const cropFocusY = cropY + (cropH * 0.3);
                
                // Convert to percentage of original image
                const centerXPercent = (cropCenterX / img.naturalWidth) * 100;
                const focusYPercent = (cropFocusY / img.naturalHeight) * 100;
                
                // Always use cover to fill the container
                img.style.objectFit = 'cover';
                
                // Apply object-position focusing on top-center of crop area
                img.style.objectPosition = centerXPercent + '% ' + focusYPercent + '%';
             })(this)">
        @else
        <img src="{{ asset('storage/'.$package->image_path) }}"
             alt="{{ $package->package_name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
        @endif
        @else
        <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
            <i class="fas fa-kaaba text-green-400 text-4xl"></i>
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
        
        <!-- Label Promo / Best Seller -->
        @if($package->is_promo || $package->is_best_seller)
        <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
            @if($package->is_promo)
            <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-red-500 text-white px-3 py-1.5 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i class='bx bxs-offer text-lg animate-pulse'></i>
                    <span class="text-xs font-bold tracking-wider">PROMO</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-20" 
                     style="animation: shimmer 2s infinite; transform: translateX(-100%);"></div>
            </div>
            @endif
            
            @if($package->is_best_seller)
            <div class="relative overflow-hidden bg-gradient-to-r from-yellow-500 to-yellow-400 text-gray-900 px-3 py-1.5 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i class='bx bxs-star text-lg'></i>
                    <span class="text-xs font-bold tracking-wider">BEST SELLER</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30" 
                     style="animation: shimmer 2s infinite; transform: translateX(-100%);"></div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Sisa Kapasitas -->
        @php
            $availableSeats = $package->getAvailableSeats();
            $capacityPercentage = ($availableSeats / max($package->capacity, 1)) * 100;
        @endphp
        <div class="absolute top-3 right-3">
            <span class="bg-white/90 backdrop-blur-sm text-xs font-semibold px-3 py-1 rounded-full
                {{ $capacityPercentage <= 20 ? 'text-red-600' : ($capacityPercentage <= 50 ? 'text-orange-600' : 'text-green-600') }}">
                <i class="fas fa-users mr-1"></i>Sisa {{ $availableSeats }} seat
            </span>
        </div>
        
        @if($package->duration_days)
        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
            <span class="text-green-600 text-xs font-semibold">
                <i class="fas fa-clock mr-1"></i>{{ $package->duration_days }} Hari
            </span>
        </div>
        @endif
        
        <!-- Badge Kategori Paket -->
        <div class="absolute bottom-3 left-3">
            <span class="bg-green-600/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full">
                {{ ucwords(str_replace('_', ' ', $package->package_subtype ?? $package->package_type)) }}
            </span>
        </div>
    </div>
    
    <!-- Content Section with Fixed Height -->
    <div class="p-5 flex flex-col" style="height: 380px;">
        <h3 class="font-bold text-gray-900 text-base mb-1 line-clamp-2 group-hover:text-green-600 transition-colors" style="min-height: 3rem;">{{ $package->package_name }}</h3>
        
        @if($package->outlet)
        <p class="text-gray-400 text-xs mb-3" style="min-height: 1.25rem;">
            <i class="fas fa-building mr-1"></i>{{ $package->outlet->nama_outlet }}
        </p>
        @else
        <div style="min-height: 1.25rem;" class="mb-3"></div>
        @endif
        
        <!-- Hotel & Maskapai Section - Fixed Height with Scroll -->
        <div class="mb-3 flex-grow overflow-y-auto" style="max-height: 180px;">
            @if($package->flightDeparture)
            <div class="flex items-center gap-2 bg-blue-50 rounded-lg px-3 py-2 mb-2">
                <div class="flex-shrink-0 w-8 h-8 bg-white rounded-md flex items-center justify-center shadow-sm">
                    <i class="fas fa-plane text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 leading-tight">Maskapai</p>
                    <p class="text-xs font-semibold text-gray-900 break-words">{{ $package->flightDeparture->airline_name ?? 'N/A' }}</p>
                </div>
            </div>
            @endif
            
            <div class="grid grid-cols-2 gap-2 mb-2">
                @if($package->hotelMakkah)
                <div class="flex items-start gap-2 bg-amber-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-amber-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight">Makkah</p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight">{{ $package->hotelMakkah->hotel_name ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif
                
                @if($package->hotelMadinah)
                <div class="flex items-start gap-2 bg-emerald-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-emerald-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight">Madinah</p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight">{{ $package->hotelMadinah->hotel_name ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Hotel Tambahan -->
            @if($package->hotels && is_array($package->hotels) && count($package->hotels) > 0)
            <div class="space-y-1">
                @foreach($package->hotels as $additionalHotel)
                <div class="flex items-start gap-2 bg-purple-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-purple-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight">{{ $additionalHotel['city'] ?? 'Hotel' }}</p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight">{{ $additionalHotel['hotel_name'] ?? 'N/A' }}</p>
                        @if(isset($additionalHotel['nights']) && $additionalHotel['nights'])
                        <p class="text-xs text-gray-400 mt-0.5">{{ $additionalHotel['nights'] }} malam</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        @if($package->ustadz_name)
        <p class="text-gray-600 text-xs mb-2 flex items-center gap-1" style="min-height: 1.25rem;">
            <i class="fas fa-user-tie text-green-600"></i>
            <span class="font-medium">{{ $package->ustadz_name }}</span>
        </p>
        @else
        <div style="min-height: 1.25rem;" class="mb-2"></div>
        @endif
        
        @if($package->departure_date)
        <p class="text-gray-500 text-xs mb-3" style="min-height: 1.25rem;">
            <i class="fas fa-calendar text-green-600 mr-1"></i>
            {{ \Carbon\Carbon::parse($package->departure_date)->format('d M Y') }}
        </p>
        @else
        <div style="min-height: 1.25rem;" class="mb-3"></div>
        @endif
        
        <div class="flex items-center justify-between pt-4 border-t border-green-50 mt-auto">
            <div>
                <div class="text-xs text-gray-400">Harga mulai</div>
                <div class="text-green-600 font-bold text-sm">
                    @if($package->price)
                        Rp {{ number_format($package->price, 0, ',', '.') }}
                    @else
                        Hubungi Kami
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</a>
