// Image optimization and loading enhancement
document.addEventListener('DOMContentLoaded', function() {
    // Handle lazy loading images
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // Add loading class
                    img.classList.add('loading');
                    
                    // Handle image load
                    img.onload = function() {
                        img.classList.remove('loading');
                        img.classList.add('loaded');
                    };
                    
                    // Handle image error
                    img.onerror = function() {
                        img.classList.remove('loading');
                        img.classList.add('error');
                        // Show placeholder icon
                        const placeholder = document.createElement('div');
                        placeholder.innerHTML = '<i class="fa fa-image" style="color: #ccc; font-size: 24px;"></i>';
                        placeholder.style.cssText = 'width: 100%; height: 100%; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px;';
                        img.parentNode.replaceChild(placeholder, img);
                    };
                    
                    observer.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach(img => {
            img.onload = function() {
                img.classList.add('loaded');
            };
        });
    }
    
    // Preload critical images
    const criticalImages = document.querySelectorAll('.listing-item-container.list-layout img[loading="lazy"]');
    criticalImages.forEach((img, index) => {
        if (index < 3) { // Preload first 3 images
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.src;
            document.head.appendChild(link);
        }
    });
    
    // Add smooth loading animation
    const style = document.createElement('style');
    style.textContent = `
        .listing-item-container.list-layout .listing-img-container {
            position: relative;
            overflow: hidden;
        }
        
        .listing-item-container.list-layout .listing-img-container img.loading {
            opacity: 0;
            transform: scale(0.95);
        }
        
        .listing-item-container.list-layout .listing-img-container img.loaded {
            opacity: 1;
            transform: scale(1);
        }
        
        .listing-item-container.list-layout .listing-img-container img.error {
            opacity: 0;
        }
        
        .listing-item-container.list-layout .listing-img-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            z-index: 0;
        }
        
        .listing-item-container.list-layout .listing-img-container img.loaded + ::after {
            display: none;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    `;
    document.head.appendChild(style);
    
    // Handle responsive thumbnails for mobile/desktop
    function handleResponsiveThumbnails() {
        const responsiveImages = document.querySelectorAll('.responsive-thumbnail');
        const isMobile = window.innerWidth <= 768;
        
        responsiveImages.forEach(function(img) {
            if (img.dataset.mobileSrc) {
                if (isMobile && img.src !== img.dataset.mobileSrc) {
                    img.src = img.dataset.mobileSrc;
                } else if (!isMobile && img.src === img.dataset.mobileSrc) {
                    // Switch back to desktop thumbnail
                    const desktopSrc = img.src.replace('/mobile-thumbnails/', '/thumbnails/').replace('_mobile_thumb', '_thumb');
                    img.src = desktopSrc;
                }
            }
        });
    }

    // Handle responsive thumbnails on load and resize
    handleResponsiveThumbnails();
    window.addEventListener('resize', handleResponsiveThumbnails);
});
