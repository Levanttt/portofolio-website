<?php
// views/about.php
?>

<!-- About Section -->
<section id="about" class="py-20 relative z-10 bg-gradient-to-b from-gray-900 to-gray-950">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold mb-16 text-center section-title font-orbitron">
            About <span class="text-blue-400">Me</span>
        </h2>
        
        <!-- Profile -->
        <div class="flex flex-col lg:flex-row gap-12 lg:items-start items-center mb-20">
            <!-- Image -->
            <div class="lg:w-1/2 flex justify-center">
                <div class="relative overflow-hidden rounded-xl glow-box bg-gray-800 w-full max-w-[600px]">
                    <?php 
                    // Ambil path dari database
                    $dbImagePath = $profile['profile_image'] ?? '';
                    
                    // Jika kosong, gunakan default
                    if (empty($dbImagePath)) {
                        $dbImagePath = 'images/profil.jpg';
                    }
                    
                    // Bersihkan path
                    $cleanPath = str_replace('\\', '/', $dbImagePath);
                    $cleanPath = ltrim($cleanPath, '/');
                    
                    // Bangun URL yang benar
                    $imgSrc = '/portofolio/public/' . $cleanPath;
                    
                    // Fallback jika gambar tidak ada
                    $fallbackImg = 'https://ui-avatars.com/api/?name=' . urlencode($profile['full_name']) . '&size=640&background=1e3a8a&color=fff';
                    ?>
                    
                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                         alt="<?= htmlspecialchars($profile['full_name']) ?>"
                         class="w-full h-auto max-h-[400px] object-cover object-center mx-auto"
                         onerror="this.src='<?= htmlspecialchars($fallbackImg) ?>';">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/30 to-transparent"></div>
                </div>
            </div>

            <!-- Text -->
            <div class="lg:w-1/2">
                <h3 class="text-2xl font-bold mb-4 text-blue-400 font-orbitron">
                    <?= htmlspecialchars($profile['title']) ?>
                </h3>
                <div class="text-gray-300 space-y-4 leading-relaxed">
                    <?= nl2br(htmlspecialchars($profile['bio'])) ?>
                </div>

                <?php if (!empty($profile['cv_url'])): ?>
                <a href="<?= htmlspecialchars($profile['cv_url']) ?>" 
                   target="_blank"
                   class="mt-8 inline-flex items-center px-6 py-2 bg-blue-800 hover:bg-blue-900 rounded-full font-medium transition-all duration-300 glow-box">
                    <i data-feather="download" class="mr-2"></i> Download CV
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Skills Section -->
        <?php include __DIR__ . '/partials/skills.php'; ?>
    </div>
</section>