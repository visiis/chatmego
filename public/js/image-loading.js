// 图片懒加载和渐进式加载脚本
document.addEventListener('DOMContentLoaded', function() {
    initImageLoading();
});

function initImageLoading() {
    // 懒加载图片
    lazyLoadImages();
    
    // 渐进式图片加载
    progressiveLoadImages();
    
    // 监听滚动事件
    window.addEventListener('scroll', debounce(loadVisibleImages, 100));
}

function lazyLoadImages() {
    const lazyImages = document.querySelectorAll('img[loading="lazy"], .lazy-image');
    
    lazyImages.forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
            // 同时给父容器添加 loaded 类
            const container = this.closest('.avatar-container');
            if (container) {
                container.classList.add('loaded');
            }
        });
        
        img.addEventListener('error', function() {
            this.style.opacity = '1';
            this.classList.add('loaded');
            const container = this.closest('.avatar-container');
            if (container) {
                container.classList.add('loaded');
            }
        });
        
        // 如果图片已经加载完成，直接添加 loaded 类
        if (img.complete && img.naturalHeight > 0) {
            img.classList.add('loaded');
            const container = img.closest('.avatar-container');
            if (container) {
                container.classList.add('loaded');
            }
        }
        
        // 如果图片已经在视口中，立即加载
        if (isInViewport(img)) {
            loadImage(img);
        }
    });
}

function progressiveLoadImages() {
    const containers = document.querySelectorAll('.progressive-image-wrapper');
    
    containers.forEach(container => {
        const img = container.querySelector('img');
        
        if (img && img.dataset.src) {
            img.addEventListener('load', function() {
                this.classList.add('loaded');
                container.classList.add('loaded');
            });
            
            if (isInViewport(container)) {
                loadProgressiveImage(img);
            }
        }
    });
}

function loadImage(img) {
    if (img.dataset.src && !img.src) {
        img.src = img.dataset.src;
    }
}

function loadProgressiveImage(img) {
    if (img.dataset.src && img.src !== img.dataset.src) {
        // 创建临时图片来加载高清版本
        const tempImg = new Image();
        tempImg.onload = function() {
            img.src = img.dataset.src;
        };
        tempImg.src = img.dataset.src;
    }
}

function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    const windowWidth = window.innerWidth || document.documentElement.clientWidth;
    
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= windowHeight + 100 &&
        rect.right <= windowWidth + 100
    );
}

function loadVisibleImages() {
    // 加载可见的懒加载图片
    document.querySelectorAll('img[loading="lazy"]:not(.loaded)').forEach(img => {
        if (isInViewport(img)) {
            loadImage(img);
        }
    });
    
    // 加载可见的渐进式图片
    document.querySelectorAll('.progressive-image-wrapper:not(.loaded)').forEach(container => {
        if (isInViewport(container)) {
            const img = container.querySelector('img');
            if (img) {
                loadProgressiveImage(img);
            }
        }
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Intersection Observer API 支持（现代浏览器）
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
                
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '100px',
        threshold: 0.1
    });
    
    // 观察所有懒加载图片
    document.querySelectorAll('img[loading="lazy"], .lazy-image').forEach(img => {
        imageObserver.observe(img);
    });
}
