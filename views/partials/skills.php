<?php
// views/partials/skills.php
?>

<!-- Skills Section -->
<div class="mb-20">
    <h3 class="text-3xl font-bold mb-12 text-center section-title">
        My <span class="text-blue-400">Skills</span>
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
        <?php foreach ($skills as $skill): ?>
        <div class="skill-icon flex flex-col items-center p-4">
            <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-2 glow-box">
                <i data-feather="<?= $skill['icon'] ?>" class="w-8 h-8 text-blue-400"></i>
            </div>
            <span class="text-gray-300 text-center"><?= $skill['name'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>