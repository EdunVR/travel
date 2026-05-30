<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['package']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['package']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<a href="<?php echo e(url('/paket/' . $package->id)); ?>" class="block">
<div class="card-hover bg-white rounded-2xl overflow-hidden border border-green-100 shadow-sm group cursor-pointer hover:shadow-xl transition-all duration-300">
    <!-- Image Section with Fixed Aspect Ratio + Lazy Loading + Blur Placeholder -->
    <div class="relative overflow-hidden bg-gray-100" style="aspect-ratio: 16/9;">
        <?php if($package->image_path): ?>
        <?php
            $cropData = $package->thumbnail_crop_settings ?? null;
            $hasValidCrop = $cropData && is_array($cropData) && isset($cropData['width']) && $cropData['width'] > 0;
            $imagePath = asset('storage/'.$package->image_path);
        ?>
        
        <!-- Blur Placeholder (shows immediately) -->
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 animate-pulse"></div>
        
        <?php if($hasValidCrop): ?>
        <?php
            // Get crop data
            $cropX = $cropData['x'] ?? 0;
            $cropY = $cropData['y'] ?? 0;
            $cropW = $cropData['width'] ?? 1;
            $cropH = $cropData['height'] ?? 1;
        ?>
        <img src="<?php echo e($imagePath); ?>"
             alt="<?php echo e($package->package_name); ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             loading="lazy"
             decoding="async"
             data-crop-x="<?php echo e($cropX); ?>"
             data-crop-y="<?php echo e($cropY); ?>"
             data-crop-w="<?php echo e($cropW); ?>"
             data-crop-h="<?php echo e($cropH); ?>"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()"
             onload="(function(img){
                // Remove placeholder
                const placeholder = img.previousElementSibling;
                if (placeholder && placeholder.classList.contains('animate-pulse')) {
                    placeholder.style.opacity = '0';
                    setTimeout(() => placeholder.remove(), 300);
                }
                
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
        <?php else: ?>
        <img src="<?php echo e($imagePath); ?>"
             alt="<?php echo e($package->package_name); ?>"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
             loading="lazy"
             decoding="async"
             onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()"
             onload="(function(img){
                // Remove placeholder
                const placeholder = img.previousElementSibling;
                if (placeholder && placeholder.classList.contains('animate-pulse')) {
                    placeholder.style.opacity = '0';
                    setTimeout(() => placeholder.remove(), 300);
                }
             })(this)">
        <?php endif; ?>
        <?php else: ?>
        <div class="w-full h-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
            <i class="fas fa-kaaba text-green-400 text-4xl"></i>
        </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
        
        <!-- Label Promo / Best Seller -->
        <?php if($package->is_promo || $package->is_best_seller): ?>
        <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
            <?php if($package->is_promo): ?>
            <div class="relative overflow-hidden bg-gradient-to-r from-red-600 to-red-500 text-white px-3 py-1.5 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i class='bx bxs-offer text-lg animate-pulse'></i>
                    <span class="text-xs font-bold tracking-wider">PROMO</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-20" 
                     style="animation: shimmer 2s infinite; transform: translateX(-100%);"></div>
            </div>
            <?php endif; ?>
            
            <?php if($package->is_best_seller): ?>
            <div class="relative overflow-hidden bg-gradient-to-r from-yellow-500 to-yellow-400 text-gray-900 px-3 py-1.5 rounded-lg shadow-lg">
                <div class="flex items-center gap-2">
                    <i class='bx bxs-star text-lg'></i>
                    <span class="text-xs font-bold tracking-wider">BEST SELLER</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30" 
                     style="animation: shimmer 2s infinite; transform: translateX(-100%);"></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Sisa Kapasitas -->
        <?php
            $availableSeats = $package->getAvailableSeats();
            $capacityPercentage = ($availableSeats / max($package->capacity, 1)) * 100;
        ?>
        <div class="absolute top-3 right-3">
            <span class="bg-white/90 backdrop-blur-sm text-xs font-semibold px-3 py-1 rounded-full
                <?php echo e($capacityPercentage <= 20 ? 'text-red-600' : ($capacityPercentage <= 50 ? 'text-orange-600' : 'text-green-600')); ?>">
                <i class="fas fa-users mr-1"></i>Sisa <?php echo e($availableSeats); ?> seat
            </span>
        </div>
        
        <?php if($package->duration_days): ?>
        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
            <span class="text-green-600 text-xs font-semibold">
                <i class="fas fa-clock mr-1"></i><?php echo e($package->duration_days); ?> Hari
            </span>
        </div>
        <?php endif; ?>
        
        <!-- Badge Kategori Paket -->
        <div class="absolute bottom-3 left-3">
            <span class="bg-green-600/90 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1 rounded-full">
                <?php echo e(ucwords(str_replace('_', ' ', $package->package_subtype ?? $package->package_type))); ?>

            </span>
        </div>
    </div>
    
    <!-- Content Section with Fixed Height -->
    <div class="p-5 flex flex-col" style="height: 380px;">
        <h3 class="font-bold text-gray-900 text-base mb-1 line-clamp-2 group-hover:text-green-600 transition-colors" style="min-height: 3rem;"><?php echo e($package->package_name); ?></h3>
        
        <?php if($package->outlet): ?>
        <p class="text-gray-400 text-xs mb-3" style="min-height: 1.25rem;">
            <i class="fas fa-building mr-1"></i><?php echo e($package->outlet->nama_outlet); ?>

        </p>
        <?php else: ?>
        <div style="min-height: 1.25rem;" class="mb-3"></div>
        <?php endif; ?>
        
        <!-- Hotel & Maskapai Section - Fixed Height with Scroll -->
        <div class="mb-3 flex-grow overflow-y-auto" style="max-height: 180px;">
            <?php if($package->flightDeparture): ?>
            <div class="flex items-center gap-2 bg-blue-50 rounded-lg px-3 py-2 mb-2">
                <div class="flex-shrink-0 w-8 h-8 bg-white rounded-md flex items-center justify-center shadow-sm">
                    <i class="fas fa-plane text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 leading-tight">Maskapai</p>
                    <p class="text-xs font-semibold text-gray-900 break-words"><?php echo e($package->flightDeparture->airline_name ?? 'N/A'); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-2 gap-2 mb-2">
                <?php if($package->hotelMakkah): ?>
                <div class="flex items-start gap-2 bg-amber-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-amber-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight">Makkah</p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight"><?php echo e($package->hotelMakkah->hotel_name ?? 'N/A'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($package->hotelMadinah): ?>
                <div class="flex items-start gap-2 bg-emerald-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-emerald-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight">Madinah</p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight"><?php echo e($package->hotelMadinah->hotel_name ?? 'N/A'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Hotel Tambahan -->
            <?php if($package->hotels && is_array($package->hotels) && count($package->hotels) > 0): ?>
            <div class="space-y-1">
                <?php $__currentLoopData = $package->hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additionalHotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start gap-2 bg-purple-50 rounded-lg px-2 py-2">
                    <div class="flex-shrink-0 w-6 h-6 bg-white rounded flex items-center justify-center shadow-sm mt-0.5">
                        <i class="fas fa-hotel text-purple-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 leading-tight"><?php echo e($additionalHotel['city'] ?? 'Hotel'); ?></p>
                        <p class="text-xs font-semibold text-gray-900 break-words leading-tight"><?php echo e($additionalHotel['hotel_name'] ?? 'N/A'); ?></p>
                        <?php if(isset($additionalHotel['nights']) && $additionalHotel['nights']): ?>
                        <p class="text-xs text-gray-400 mt-0.5"><?php echo e($additionalHotel['nights']); ?> malam</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if($package->ustadz_name): ?>
        <p class="text-gray-600 text-xs mb-2 flex items-center gap-1" style="min-height: 1.25rem;">
            <i class="fas fa-user-tie text-green-600"></i>
            <span class="font-medium"><?php echo e($package->ustadz_name); ?></span>
        </p>
        <?php else: ?>
        <div style="min-height: 1.25rem;" class="mb-2"></div>
        <?php endif; ?>
        
        <?php if($package->departure_date): ?>
        <p class="text-gray-500 text-xs mb-3" style="min-height: 1.25rem;">
            <i class="fas fa-calendar text-green-600 mr-1"></i>
            <?php echo e(\Carbon\Carbon::parse($package->departure_date)->format('d M Y')); ?>

        </p>
        <?php else: ?>
        <div style="min-height: 1.25rem;" class="mb-3"></div>
        <?php endif; ?>
        
        <div class="flex items-center justify-between pt-4 border-t border-green-50 mt-auto">
            <div>
                <div class="text-xs text-gray-400">Harga mulai</div>
                <div class="text-green-600 font-bold text-sm">
                    <?php if($package->price): ?>
                        Rp <?php echo e(number_format($package->price, 0, ',', '.')); ?>

                    <?php else: ?>
                        Hubungi Kami
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</a>
<?php /**PATH C:\xampp\htdocs\hm\resources\views/components/package-card.blade.php ENDPATH**/ ?>