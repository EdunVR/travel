<?php
// Final fix for crop display - use CSS positioning

$file = 'resources/views/homepage.blade.php';
$content = file_get_contents($file);

$old = <<<'OLD'
                    @if($hasValidCrop)
                    <!-- Tampilkan gambar dengan crop settings -->
                    <img src="{{ asset('storage/'.$pkg->image_path) }}"
                         alt="{{ $pkg->package_name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 crop-display-image"
                         data-crop='@json($cropData)'
                         onerror="this.parentElement.style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
OLD;

$new = <<<'NEW'
                    @if($hasValidCrop)
                    <!-- Tampilkan gambar dengan crop settings menggunakan wrapper clip -->
                    @php
                        $cropX = $cropData['x'] ?? 0;
                        $cropY = $cropData['y'] ?? 0;
                        $cropW = $cropData['width'] ?? 1;
                        $cropH = $cropData['height'] ?? 1;
                    @endphp
                    <div style="position: relative; width: 100%; height: 100%; overflow: hidden;">
                        <img src="{{ asset('storage/'.$pkg->image_path) }}"
                             alt="{{ $pkg->package_name }}"
                             class="group-hover:scale-105 transition-transform duration-500"
                             style="position: absolute;
                                    left: {{ -$cropX }}px;
                                    top: {{ -$cropY }}px;
                                    min-width: {{ $cropW }}px;
                                    min-height: {{ $cropH }}px;
                                    width: auto;
                                    height: auto;"
                             onerror="this.closest('div').style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                    </div>
NEW;

$count = substr_count($content, $old);
echo "Found {$count} occurrences\n";

$newContent = str_replace($old, $new, $content);
file_put_contents($file, $newContent);

echo "Replaced successfully!\n";
