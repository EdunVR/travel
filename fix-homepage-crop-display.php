<?php
// Script to fix crop display in homepage

$file = 'resources/views/homepage.blade.php';
$content = file_get_contents($file);

// Pattern to find and replace
$oldPattern = <<<'OLD'
                    @if($hasValidCrop)
                    <!-- Tampilkan gambar dengan crop settings -->
                    @php
                        $cropX = $cropData['x'] ?? 0;
                        $cropY = $cropData['y'] ?? 0;
                        $cropWidth = $cropData['width'] ?? 1;
                        $cropHeight = $cropData['height'] ?? 1;
                        $rotate = $cropData['rotate'] ?? 0;
                        $imgScaleX = $cropData['scaleX'] ?? 1;
                        $imgScaleY = $cropData['scaleY'] ?? 1;
                    @endphp
                    <img src="{{ asset('storage/'.$pkg->image_path) }}"
                         alt="{{ $pkg->package_name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         style="object-position: {{ $cropX + $cropWidth/2 }}px {{ $cropY + $cropHeight/2 }}px;"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()"
                         onload="
                            const img = this;
                            const container = img.parentElement;
                            const containerW = container.offsetWidth;
                            const containerH = container.offsetHeight;
                            const cropData = {{ json_encode($cropData) }};
                            
                            // Calculate scale to make crop area fill container
                            const scaleX = containerW / cropData.width;
                            const scaleY = containerH / cropData.height;
                            const scale = Math.max(scaleX, scaleY);
                            
                            // Calculate position
                            const offsetX = -cropData.x * scale + (containerW - img.naturalWidth * scale) / 2;
                            const offsetY = -cropData.y * scale + (containerH - img.naturalHeight * scale) / 2;
                            
                            // Apply styles
                            img.style.width = (img.naturalWidth * scale) + 'px';
                            img.style.height = (img.naturalHeight * scale) + 'px';
                            img.style.objectFit = 'none';
                            img.style.objectPosition = offsetX + 'px ' + offsetY + 'px';
                            img.style.transform = 'rotate(' + cropData.rotate + 'deg) scaleX(' + cropData.scaleX + ') scaleY(' + cropData.scaleY + ')';
                         ">
OLD;

$newPattern = <<<'NEW'
                    @if($hasValidCrop)
                    <!-- Tampilkan gambar dengan crop settings -->
                    <img src="{{ asset('storage/'.$pkg->image_path) }}"
                         alt="{{ $pkg->package_name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 crop-display-image"
                         data-crop='@json($cropData)'
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
NEW;

// Count occurrences
$count = substr_count($content, $oldPattern);
echo "Found {$count} occurrences\n";

// Replace all occurrences
$newContent = str_replace($oldPattern, $newPattern, $content);

// Save
file_put_contents($file, $newContent);

echo "Replaced all occurrences successfully!\n";
echo "File saved: {$file}\n";
