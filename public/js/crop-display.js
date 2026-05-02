/**
 * Crop Display Helper - Simple and Reliable
 * Shows the cropped area by positioning the image correctly
 */

function applyCropDisplay(img) {
  try {
    const cropDataStr = img.getAttribute('data-crop');
    if (!cropDataStr) return;
    
    const cropData = JSON.parse(cropDataStr);
    if (!cropData || !cropData.width || cropData.width === 0) return;
    
    // Wait for natural dimensions
    if (!img.naturalWidth || !img.naturalHeight) return;
    
    // Calculate center of crop area as percentage of original image
    const cropCenterX = cropData.x + (cropData.width / 2);
    const cropCenterY = cropData.y + (cropData.height / 2);
    const centerXPercent = (cropCenterX / img.naturalWidth) * 100;
    const centerYPercent = (cropCenterY / img.naturalHeight) * 100;
    
    // Use object-fit cover with object-position at crop center
    img.style.objectFit = 'cover';
    img.style.objectPosition = centerXPercent + '% ' + centerYPercent + '%';
    
    // Apply rotation if any
    if (cropData.rotate || cropData.scaleX !== 1 || cropData.scaleY !== 1) {
      img.style.transform = 'rotate(' + (cropData.rotate || 0) + 'deg) scaleX(' + (cropData.scaleX || 1) + ') scaleY(' + (cropData.scaleY || 1) + ')';
    }
    
  } catch (e) {
    console.error('Error applying crop:', e);
  }
}

// Auto-apply on page load
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.crop-display-image').forEach(function(img) {
    if (img.complete && img.naturalWidth) {
      applyCropDisplay(img);
    } else {
      img.addEventListener('load', function() {
        applyCropDisplay(this);
      });
    }
  });
});
