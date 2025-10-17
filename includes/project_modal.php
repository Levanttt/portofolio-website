<?php
// includes/project_modal.php - Modal Component for Project Detail
// This file is included in loop, $project variable is available

$tech_stack = !empty($project['tech_stack']) ? pgArrayToPhp($project['tech_stack']) : [];
$features = !empty($project['features']) ? pgArrayToPhp($project['features']) : [];
$gallery = !empty($project['gallery_images']) ? pgArrayToPhp($project['gallery_images']) : [];

// Prepare all images (main + gallery)
$allImages = [$project['image_url']];
if (!empty($gallery)) {
    $allImages = array_merge($allImages, $gallery);
}
?>

<!-- Modal -->
<div id="modal-<?= $projectId ?>" class="hidden fixed inset-0 z-[100] overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm" 
         onclick="closeProjectModal('<?= $projectId ?>')"></div>
    
    <!-- Modal Content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-gray-900 rounded-2xl max-w-3xl w-full max-h-[85vh] overflow-y-auto shadow-2xl border border-gray-700 scale-95 transition-transform duration-300">
            
            <!-- Close Button -->
            <button onclick="closeProjectModal('<?= $projectId ?>')" 
                    class="absolute top-3 right-3 z-10 w-8 h-8 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center transition">
                <i data-feather="x" class="w-5 h-5 text-white"></i>
            </button>
            
            <!-- Media Section with Gallery -->
            <div class="relative w-full bg-gray-800 rounded-t-2xl overflow-hidden">
                <!-- Main Large Image Display -->
                <div class="relative w-full h-[350px]" id="mainImage-<?= $projectId ?>">
                    <?php if ($project['media_type'] === 'youtube' && !empty($project['media_url'])): ?>
                        <iframe id="youtube-<?= $projectId ?>"
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($project['media_url']) ?>?autoplay=1&mute=1" 
                            class="w-full h-full"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    <?php else: ?>
                        <img id="displayImage-<?= $projectId ?>"
                             src="<?= htmlspecialchars($allImages[0]) ?>" 
                             alt="<?= htmlspecialchars($project['title']) ?>" 
                             class="w-full h-full object-contain cursor-zoom-in transition-opacity duration-300"
                             onclick="openFullscreen('<?= $projectId ?>', 0)">
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnail Gallery -->
                <?php if (count($allImages) > 1): ?>
                <div class="relative bg-gray-900 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <!-- Left Arrow -->
                        <button onclick="scrollGallery('<?= $projectId ?>', -1)" 
                                class="flex-shrink-0 w-8 h-16 bg-gray-800 hover:bg-gray-700 rounded flex items-center justify-center transition">
                            <i data-feather="chevron-left" class="w-5 h-5 text-white"></i>
                        </button>
                        
                        <!-- Scrollable Thumbnails -->
                        <div id="thumbContainer-<?= $projectId ?>" 
                             class="flex gap-2 overflow-x-hidden overflow-y-hidden scroll-smooth flex-1 scrollbar-hide">
                            <?php foreach ($allImages as $index => $imgUrl): ?>
                                <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                     alt="Screenshot <?= $index + 1 ?>"
                                     onclick="changeMainImage('<?= $projectId ?>', <?= $index ?>)"
                                     id="thumb-<?= $projectId ?>-<?= $index ?>"
                                     class="w-24 h-16 object-cover rounded cursor-pointer border-2 transition-all duration-300 
                                            <?= $index === 0 ? 'border-indigo-500 opacity-100 scale-105' : 'border-gray-600 opacity-60' ?> 
                                            hover:opacity-100 hover:border-indigo-400">
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Right Arrow -->
                        <button onclick="scrollGallery('<?= $projectId ?>', 1)" 
                                class="flex-shrink-0 w-8 h-16 bg-gray-800 hover:bg-gray-700 rounded flex items-center justify-center transition">
                            <i data-feather="chevron-right" class="w-5 h-5 text-white"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Content Section -->
            <div class="p-6">
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-1 font-orbitron">
                            <?= htmlspecialchars($project['title']) ?>
                        </h2>
                        <?php if ($project['is_featured']): ?>
                            <span class="inline-flex items-center px-3 py-1 bg-yellow-900 text-yellow-300 rounded-full text-xs">
                                <i data-feather="star" class="w-3 h-3 mr-1"></i> Featured Project
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="text-xl text-gray-400 font-bold"><?= $project['year'] ?></span>
                </div>
                
                <div class="flex flex-wrap gap-2 mb-5">
                    <?php foreach ($tags as $tag): ?>
                        <span class="px-3 py-1 bg-indigo-900 text-indigo-300 rounded-full text-xs">
                            <?= htmlspecialchars($tag) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <div class="mb-5">
                    <h3 class="text-base font-bold text-white mb-2">My Roles</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($roles as $role): ?>
                            <span class="px-3 py-1 bg-gray-800 rounded-lg flex items-center gap-2 text-sm">
                                <div class="w-2.5 h-2.5 rounded-full <?= getRoleBadgeColor($role) ?>"></div>
                                <span class="text-gray-300"><?= getRoleName($role) ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="mb-5">
                    <h3 class="text-base font-bold text-white mb-2">About This Project</h3>
                    <p class="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>
                </div>
                
                <?php if (!empty($tech_stack)): ?>
                <div class="mb-5">
                    <h3 class="text-base font-bold text-white mb-2">Tech Stack</h3>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <?php foreach ($tech_stack as $tech): ?>
                            <span class="px-3 py-1 bg-purple-900 bg-opacity-50 text-purple-300 rounded">
                                <?= htmlspecialchars($tech) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($features)): ?>
                <div class="mb-5">
                    <h3 class="text-base font-bold text-white mb-2">Key Features</h3>
                    <ul class="space-y-1 text-sm">
                        <?php foreach ($features as $feature): ?>
                            <li class="flex items-start gap-2 text-gray-300">
                                <i data-feather="check-circle" class="w-4 h-4 text-indigo-400 flex-shrink-0 mt-0.5"></i>
                                <span><?= htmlspecialchars($feature) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-800">
                    <?php if (!empty($project['project_url'])): ?>
                        <a href="<?= htmlspecialchars($project['project_url']) ?>" 
                           target="_blank"
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                            <i data-feather="external-link" class="w-4 h-4"></i>
                            Live Project
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['github_url'])): ?>
                        <a href="<?= htmlspecialchars($project['github_url']) ?>" 
                           target="_blank"
                           class="px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                            <i data-feather="github" class="w-4 h-4"></i>
                            GitHub
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['demo_url'])): ?>
                        <a href="<?= htmlspecialchars($project['demo_url']) ?>" 
                           target="_blank"
                           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                            <i data-feather="play-circle" class="w-4 h-4"></i>
                            Play Demo
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
