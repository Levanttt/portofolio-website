<?php
// views/about.php
?>

<!-- About Section -->
<section id="about" class="py-20 relative z-10 bg-gradient-to-b from-gray-900 to-gray-950">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold mb-16 text-center section-title font-orbitron">
            About <span class="text-indigo-400">Me</span>
        </h2>
        
        <div class="flex flex-col lg:flex-row gap-12 items-center mb-20">
            <div class="lg:w-1/2">
                <div class="relative overflow-hidden rounded-xl glow-box">
                    <img src="<?= $profile['profile_image'] ?? 'http://static.photos/technology/640x360/42' ?>" alt="<?= $profile['full_name'] ?>" class="w-full h-auto">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
                </div>
            </div>
            <div class="lg:w-1/2">
                <h3 class="text-2xl font-bold mb-4 text-indigo-400 font-orbitron"><?= $profile['title'] ?></h3>
                <div class="text-gray-300 space-y-4">
                    <?= nl2br($profile['bio']) ?>
                </div>
                <?php if ($profile['cv_url']): ?>
                <a href="<?= $profile['cv_url'] ?>" class="mt-8 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-full font-medium inline-flex items-center transition-all duration-300 transform hover:scale-105 glow-box">
                    <i data-feather="download" class="mr-2"></i> Download CV
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Skills Section -->
        <?php include __DIR__ . '/partials/skills.php'; ?>
    </div>
</section>