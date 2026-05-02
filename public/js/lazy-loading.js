/**
 * Lazy Loading Implementation for Images
 * Optimizes page load performance by loading images only when needed
 */

class LazyLoader {
    constructor(options = {}) {
        this.options = {
            rootMargin: "50px 0px",
            threshold: 0.01,
            loadingClass: "lazy-loading",
            loadedClass: "lazy-loaded",
            errorClass: "lazy-error",
            placeholder:
                "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+",
            ...options,
        };

        this.observer = null;
        this.images = new Set();
        this.init();
    }

    init() {
        if (!("IntersectionObserver" in window)) {
            // Fallback for older browsers
            this.loadAllImages();
            return;
        }

        this.observer = new IntersectionObserver(
            this.handleIntersection.bind(this),
            {
                rootMargin: this.options.rootMargin,
                threshold: this.options.threshold,
            },
        );

        this.observeImages();
    }

    observeImages() {
        // Find all images with data-src attribute
        const lazyImages = document.querySelectorAll("img[data-src]");

        lazyImages.forEach((img) => {
            this.images.add(img);
            this.observer.observe(img);

            // Add loading class
            img.classList.add(this.options.loadingClass);

            // Set placeholder if no src is set
            if (!img.src || img.src === window.location.href) {
                img.src = this.options.placeholder;
            }
        });

        console.log(`🖼️ LazyLoader: Observing ${lazyImages.length} images`);
    }

    handleIntersection(entries) {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                this.loadImage(entry.target);
            }
        });
    }

    loadImage(img) {
        const src = img.dataset.src;
        const srcset = img.dataset.srcset;

        if (!src) return;

        // Create a new image to preload
        const imageLoader = new Image();

        imageLoader.onload = () => {
            // Image loaded successfully
            img.src = src;
            if (srcset) {
                img.srcset = srcset;
            }

            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.loadedClass);

            // Remove data attributes
            delete img.dataset.src;
            if (srcset) delete img.dataset.srcset;

            // Stop observing this image
            this.observer.unobserve(img);
            this.images.delete(img);
        };

        imageLoader.onerror = () => {
            // Image failed to load
            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.errorClass);

            // Set error placeholder
            img.src = this.getErrorPlaceholder();
            img.alt = "Failed to load image";

            this.observer.unobserve(img);
            this.images.delete(img);
        };

        // Start loading
        imageLoader.src = src;
        if (srcset) {
            imageLoader.srcset = srcset;
        }
    }

    getErrorPlaceholder() {
        return "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjI0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1IiBzdHJva2U9IiNkZGQiIHN0cm9rZS13aWR0aD0iMiIvPjx0ZXh0IHg9IjUwJSIgeT0iNDAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkltYWdlPC90ZXh0Pjx0ZXh0IHg9IjUwJSIgeT0iNjAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiNjY2MiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vdCBGb3VuZDwvdGV4dD48L3N2Zz4=";
    }

    loadAllImages() {
        // Fallback: load all images immediately
        const lazyImages = document.querySelectorAll("img[data-src]");

        lazyImages.forEach((img) => {
            const src = img.dataset.src;
            const srcset = img.dataset.srcset;

            if (src) {
                img.src = src;
                delete img.dataset.src;
            }

            if (srcset) {
                img.srcset = srcset;
                delete img.dataset.srcset;
            }

            img.classList.add(this.options.loadedClass);
        });
    }

    // Add new images to observer (for dynamic content)
    observe(img) {
        if (this.observer && img.dataset.src) {
            this.images.add(img);
            this.observer.observe(img);
            img.classList.add(this.options.loadingClass);

            if (!img.src || img.src === window.location.href) {
                img.src = this.options.placeholder;
            }
        }
    }

    // Remove image from observer
    unobserve(img) {
        if (this.observer) {
            this.observer.unobserve(img);
            this.images.delete(img);
        }
    }

    // Destroy the lazy loader
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
        this.images.clear();
    }

    // Get statistics
    getStats() {
        return {
            observing: this.images.size,
            supported: "IntersectionObserver" in window,
        };
    }
}

// Auto-initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    window.lazyLoader = new LazyLoader({
        rootMargin: "100px 0px", // Start loading 100px before image enters viewport
        threshold: 0.01,
    });

    console.log("✅ LazyLoader initialized");
});

// Helper function to convert regular img to lazy loading
window.makeLazyImage = function (img, src, srcset = null) {
    img.dataset.src = src;
    if (srcset) {
        img.dataset.srcset = srcset;
    }

    if (window.lazyLoader) {
        window.lazyLoader.observe(img);
    } else {
        // Fallback if lazy loader not ready
        img.src = src;
        if (srcset) img.srcset = srcset;
    }
};

// CSS for loading states
const lazyStyles = `
<style>
.lazy-loading {
    background: #f5f5f5;
    background-image: linear-gradient(90deg, #f5f5f5 25%, #e0e0e0 50%, #f5f5f5 75%);
    background-size: 200% 100%;
    animation: lazy-shimmer 1.5s infinite;
}

.lazy-loaded {
    animation: lazy-fade-in 0.3s ease-in-out;
}

.lazy-error {
    background: #f8f8f8;
    border: 1px dashed #ddd;
}

@keyframes lazy-shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@keyframes lazy-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
`;

// Inject CSS
document.head.insertAdjacentHTML("beforeend", lazyStyles);
