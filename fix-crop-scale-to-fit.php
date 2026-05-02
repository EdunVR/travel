<?php
// Fix crop display dengan scale agar fit ke container

$file = 'resources/views/homepage.blade.php';
$content = file_get_contents($file);

$old = <<<'OLD'
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
OLD;

$new = <<<'NEW'
                    @if($hasValidCrop)
                    <!-- Tampilkan gambar dengan crop settings - scaled to fit -->
                    @php
                        $cropX = $cropData['x'] ?? 0;
                        $cropY = $cropData['y'] ?? 0;
                        $cropW = $cropData['width'] ?? 1;
                        $cropH = $cropData['height'] ?? 1;
                        
                        // Container dimensions (h-48 = 192px, width varies but typically ~400px)
                        // We'll use CSS to scale, but calculate the transform origin
                        $originX = $cropX + ($cropW / 2);
                        $originY = $cropY + ($cropH / 2);
                    @endphp
                    <div style="position: relative; width: 100%; height: 100%; overflow: hidden; background: #f5f5f5;">
                        <!-- Inner wrapper for scaling -->
                        <div style="position: absolute; 
                                    left: 50%; 
                                    top: 50%; 
                                    width: {{ $cropW }}px; 
                                    height: {{ $cropH }}px;
                                    transform: translate(-50%, -50%);">
                            <img src="{{ asset('storage/'.$pkg->image_path) }}"
                                 alt="{{ $pkg->package_name }}"
                                 class="group-hover:scale-105 transition-transform duration-500"
                                 style="position: absolute;
                                        left: {{ -$cropX }}px;
                                        top: {{ -$cropY }}px;
                                        width: auto;
                                        height: auto;"
                                 onerror="this.closest('[style*=background]').style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()">
                        </div>
                    </div>
NEW;

$count = substr_count($content, $old);
echo "Found {$count} occurrences\n";

if ($count > 0) {
    $newContent = str_replace($old, $new, $content);
    file_put_contents($file, $newContent);
    echo "Replaced successfully!\n";
} else {
    echo "Pattern not found!\n";
}
