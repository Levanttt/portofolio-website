<?php
// views/home.php
?>

<!-- Hero Section -->
<section id="home" class="relative min-h-screen flex items-center justify-center z-10">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 font-orbitron glow-text">
            <span class="text-indigo-400"><?= explode(' ', $profile['full_name'])[0] ?></span> 
            <span class="text-purple-400"><?= explode(' ', $profile['full_name'])[1] ?? '' ?></span> 
            <span class="text-white"><?= explode(' ', $profile['full_name'])[2] ?? '' ?></span>
        </h1>
        <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto">
            I build interactive experiences — one line of code and one design at a time.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-12">
            <a href="?page=projects" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-full font-medium transition-all duration-300 transform hover:scale-105 glow-box">
                View My Projects
            </a>
            <?php if ($profile['cv_url']): ?>
            <a href="<?= $profile['cv_url'] ?>" class="px-8 py-3 border-2 border-indigo-600 hover:bg-indigo-900 hover:bg-opacity-30 rounded-full font-medium transition-all duration-300 transform hover:scale-105">
                Download CV
            </a>
            <?php endif; ?>
        </div>
        <div class="flex justify-center gap-4 text-gray-400 flex-wrap">
            <span class="flex items-center"><i data-feather="edit-3" class="mr-2 text-indigo-400"></i> Game Designer</span>
            <span class="flex items-center"><i data-feather="code" class="mr-2 text-purple-400"></i> Game Programmer</span>
        </div>
    </div>
    <div class="absolute bottom-10 left-0 right-0 flex justify-center animate-bounce">
        <a href="?page=about" class="text-white">
            <i data-feather="chevron-down" class="w-8 h-8"></i>
        </a>
    </div>
</section>

<?php
// Include sections lainnya
include __DIR__ . '/about.php';
include __DIR__ . '/projects.php'; 
include __DIR__ . '/contact.php';
?>