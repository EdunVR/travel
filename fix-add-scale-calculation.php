<?php
// Add scale calculation to make crop area fit container

$file = 'resources/views/homepage.blade.php';
$content = file_get_contents($file);

$old = <<<'OLD'
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
OLD;

$new = <<<'NEW'
                    <div class="crop-container" style="position: relative; width: 100%; height: 100%; overflow: hidden; background: #f5f5f5;">
                        <!-- Inner wrapper for scaling -->
                        <div class="crop-wrapper" 
                             data-crop-w="{{ $cropW }}" 
                             data-crop-h="{{ $cropH }}"
                             style="position: absolute; 
                                    left: 50%; 
                                    top: 50%; 
                                    width: {{ $cropW }}px; 
                                    height: {{ $cropH }}px;
                                    transform-origin: center center;">
                            <img src="{{ asset('storage/'.$pkg->image_path) }}"
                                 alt="{{ $pkg->package_name }}"
                                 class="group-hover:scale-105 transition-transform duration-500"
                                 style="position: absolute;
                                        left: {{ -$cropX }}px;
                                        top: {{ -$cropY }}px;
                                        width: auto;
                                        height: auto;"
                                 onerror="this.closest('.crop-container').style.background='linear-gradient(135deg,#e8f5e9,#c8e6c9)';this.remove()"
                                 onload="(function(img){
                                    const wrapper = img.closest('.crop-wrapper');
                                    const container = img.closest('.crop-container');
                                    if (!wrapper || !container) return;
                                    const cropW = parseFloat(wrapper.dataset.cropW);
                                    const cropH = parseFloat(wrapper.dataset.cropH);
                                    const containerW = container.offsetWidth;
                                    const containerH = container.offsetHeight;
                                    const scaleX = containerW / cropW;
                                    const scaleY = containerH / cropH;
                                    const scale = Math.max(scaleX, scaleY);
                                    wrapper.style.transform = 'translate(-50%, -50%) scale(' + scale + ')';
                                 })(this)">
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
    echo "Pattern not found! Trying to find similar pattern...\n";
    // Show what we have
    if (strpos($content, 'crop-container') !== false) {
        echo "Already has crop-container class\n";
    }
}
