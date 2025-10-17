// public/assets/js/gallery.js - Gallery Management System

// Global storage untuk gallery data (mirip projectImages di modal)
window.projectGalleries = {};

// Initialize gallery ketika modal dibuka
function initializeGallery(projectId) {
    console.log('🔧 Initializing gallery for:', projectId);

    const modal = document.getElementById('modal-' + projectId);
    if (!modal || modal.classList.contains('hidden')) {
        console.error('❌ Modal not found or hidden');
        return;
    }

    // Collect all images dari thumbnail container
    const images = [];
    const thumbContainer = document.getElementById('thumbContainer-' + projectId);

    if (thumbContainer) {
        thumbContainer.querySelectorAll('img').forEach(img => {
            if (img.src) images.push(img.src);
        });
    }

    // Fallback: ambil dari main image
    if (images.length === 0) {
        const mainImg = document.getElementById('displayImage-' + projectId);
        if (mainImg && mainImg.src) {
            images.push(mainImg.src);
        }
    }

    if (images.length > 0) {
        window.projectGalleries[projectId] = images;

        // **Tambahan: Paksa main image ke gambar pertama**
        const mainImg = document.getElementById('displayImage-' + projectId);
        if (mainImg) {
            mainImg.src = images[0];
            mainImg.style.opacity = '1';
            console.log('🔄 Main image reset to first image:', images[0]);
        }

        setupThumbnailClicks(projectId);
        updateThumbnailBorders(projectId, 0);

        return true;
    } else {
        console.error('❌ No images found for gallery!');
        return false;
    }
}

// Setup click event untuk thumbnails
function setupThumbnailClicks(projectId) {
    const thumbContainer = document.getElementById('thumbContainer-' + projectId);
    if (!thumbContainer) return;
    
    const thumbs = thumbContainer.querySelectorAll('img');
    
    thumbs.forEach((thumb, index) => {
        thumb.style.cursor = 'pointer';
        thumb.style.transition = 'all 0.3s ease';
        
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🖱️ Thumbnail clicked! Index:', index);
            changeMainImage(projectId, index);
        });
    });
    
    console.log('✅ Added click listeners to', thumbs.length, 'thumbnails');
}

// Change main display image
function changeMainImage(projectId, index) {
    console.log('🖼️ Changing to index:', index);
    
    const images = window.projectGalleries[projectId];
    const mainImg = document.getElementById('displayImage-' + projectId);
    
    if (mainImg && images && images[index]) {
        // Fade out
        mainImg.style.opacity = '0';
        
        setTimeout(() => {
            mainImg.src = images[index];
            // Fade in
            mainImg.style.opacity = '1';
        }, 150);
    }
    
    // Update thumbnail highlights
    updateThumbnailBorders(projectId, index);
}

// Update thumbnail borders (dedicated function)
function updateThumbnailBorders(projectId, activeIndex) {
    const thumbs = document.querySelectorAll(`[id^="thumb-${projectId}-"]`);
    
    console.log('🎨 Updating borders for index:', activeIndex, 'Total thumbs:', thumbs.length);
    
    thumbs.forEach((thumb, i) => {
        // Remove all classes first
        thumb.classList.remove('border-indigo-500', 'border-gray-600', 'opacity-100', 'opacity-60', 'scale-105');
        
        if (i === activeIndex) {
            // Active state - BORDER BIRU TEBAL
            thumb.classList.add('border-indigo-500', 'opacity-100', 'scale-105');
            thumb.style.borderWidth = '3px';
            thumb.style.borderColor = '#6366f1'; // Indigo-500
            thumb.style.opacity = '1';
            thumb.style.transform = 'scale(1.05)';
            console.log('  ✨ Active:', i);
        } else {
            // Inactive state - BORDER ABU TIPIS
            thumb.classList.add('border-gray-600', 'opacity-60');
            thumb.style.borderWidth = '2px';
            thumb.style.borderColor = '#4b5563'; // Gray-600
            thumb.style.opacity = '0.6';
            thumb.style.transform = 'scale(1)';
        }
    });
    
    console.log('✅ Borders updated!');
}

// Scroll gallery thumbnails & change main image
function scrollGallery(projectId, direction) {
    const images = window.projectGalleries[projectId];
    if (!images) return;
    
    const container = document.getElementById('thumbContainer-' + projectId);
    const thumbs = container.querySelectorAll('img');
    
    // Find current active index
    let activeIndex = 0;
    thumbs.forEach((thumb, i) => {
        if (thumb.classList.contains('border-indigo-500') || 
            thumb.style.borderColor === 'rgb(99, 102, 241)') {
            activeIndex = i;
        }
    });
    
    // Calculate new index
    let newIndex = activeIndex + direction;
    if (newIndex < 0) newIndex = 0;
    if (newIndex >= images.length) newIndex = images.length - 1;
    
    console.log('⬅️➡️ Arrow clicked! From:', activeIndex, 'To:', newIndex);
    
    // Change main image & highlight thumbnail
    changeMainImage(projectId, newIndex);
    
    // Scroll thumbnail container
    const scrollAmount = 110; // width + gap
    container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

// Fullscreen image view
function openFullscreen(projectId, index) {
    const images = window.projectGalleries[projectId];
    if (!images || !images[index]) return;
    
    const overlay = document.createElement('div');
    overlay.id = 'fullscreen-' + projectId;
    overlay.className = 'fixed inset-0 z-[200] bg-black bg-opacity-95 flex items-center justify-center p-4';
    overlay.innerHTML = `
        <button onclick="closeFullscreen('${projectId}')" 
                class="absolute top-4 right-4 w-12 h-12 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center z-10 transition">
            <i data-feather="x" class="w-6 h-6 text-white"></i>
        </button>
        
        <button onclick="navigateFullscreen('${projectId}', -1)" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center z-10 transition">
            <i data-feather="chevron-left" class="w-6 h-6 text-white"></i>
        </button>
        
        <img id="fullscreen-img-${projectId}" 
             src="${images[index]}" 
             class="max-w-full max-h-full object-contain" 
             alt="Fullscreen view">
        
        <button onclick="navigateFullscreen('${projectId}', 1)" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 w-12 h-12 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center z-10 transition">
            <i data-feather="chevron-right" class="w-6 h-6 text-white"></i>
        </button>
        
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-75 px-4 py-2 rounded-full text-white">
            <span id="fullscreen-counter-${projectId}">${index + 1}</span> / ${images.length}
        </div>
    `;
    
    document.body.appendChild(overlay);
    if (typeof feather !== 'undefined') feather.replace();
    
    overlay.dataset.currentIndex = index;
    
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeFullscreen(projectId);
    });
}

// Navigate in fullscreen
function navigateFullscreen(projectId, direction) {
    const overlay = document.getElementById('fullscreen-' + projectId);
    const images = window.projectGalleries[projectId];
    
    if (!overlay || !images) return;
    
    let currentIndex = parseInt(overlay.dataset.currentIndex);
    let newIndex = currentIndex + direction;
    
    if (newIndex < 0) newIndex = images.length - 1;
    if (newIndex >= images.length) newIndex = 0;
    
    const img = document.getElementById('fullscreen-img-' + projectId);
    const counter = document.getElementById('fullscreen-counter-' + projectId);
    
    if (img) {
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = images[newIndex];
            img.style.opacity = '1';
        }, 150);
    }
    
    if (counter) {
        counter.textContent = newIndex + 1;
    }
    
    overlay.dataset.currentIndex = newIndex;
}

// Close fullscreen
function closeFullscreen(projectId) {
    const overlay = document.getElementById('fullscreen-' + projectId);
    if (overlay) overlay.remove();
}

// Initialize when DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth transition to all display images
    document.querySelectorAll('[id^="displayImage-"]').forEach(img => {
        img.style.transition = 'opacity 0.3s ease';
    });
});

// Override openProjectModal untuk auto-initialize gallery
window.openProjectModal = function(projectId) {
    console.log('📂 Opening modal:', projectId);
    
    const modal = document.getElementById('modal-' + projectId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize gallery AFTER modal is visible
        setTimeout(() => {
            initializeGallery(projectId);
            
            // Reinitialize feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }, 100);
    }
};

// Close modal function
window.closeProjectModal = function(projectId) {
    const modal = document.getElementById('modal-' + projectId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Reset main image src ke gambar pertama
        const mainImg = document.getElementById('displayImage-' + projectId);
        if (mainImg && window.projectGalleries[projectId] && window.projectGalleries[projectId][0]) {
            mainImg.src = window.projectGalleries[projectId][0];
            mainImg.style.opacity = '1';
        }

        // Reset border aktif thumbnail
        if (window.projectGalleries[projectId]) {
            changeMainImage(projectId, 0);
        }

        // Clean up gallery data jika perlu
        delete window.projectGalleries[projectId];
    }
};

// Close on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Find all open modals
        document.querySelectorAll('[id^="modal-"]').forEach(modal => {
            // Ambil projectId dari id modal
            const projectId = modal.id.replace('modal-', '');
            modal.classList.add('hidden');
            // Reset main image ke index 0
            if (window.projectGalleries[projectId]) {
                changeMainImage(projectId, 0);
            }
            // Clean up gallery data jika perlu
            delete window.projectGalleries[projectId];
        });
        document.body.style.overflow = 'auto';

        // Close fullscreen if open
        const fullscreen = document.querySelector('[id^="fullscreen-"]');
        if (fullscreen) {
            fullscreen.remove();
        }
    }
});

console.log('✅ Gallery system loaded!');