<?php
// includes/project_modal.php - Modal Component for Project Detail
// This file is included in loop, $project variable is available

$tech_stack = !empty($project['tech_stack']) ? pgArrayToPhp($project['tech_stack']) : [];
$features = !empty($project['features']) ? pgArrayToPhp($project['features']) : [];
$gallery = !empty($project['gallery_images']) ? pgArrayToPhp($project['gallery_images']) : [];
?>

<!-- Modal -->
<div id="modal-<?= $projectId ?>" class="hidden fixed inset-0 z-[100] overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm" 
        onclick="closeProjectModal('<?= $projectId ?>')"></div>
    
    <!-- Modal Content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-gray-900 rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-700">
            
            <!-- Close Button -->
            <button onclick="closeProjectModal('<?= $projectId ?>')" 
                    class="absolute top-4 right-4 z-10 w-10 h-10 bg-gray-800 hover:bg-gray-700 rounded-full flex items-center justify-center transition">
                <i data-feather="x" class="w-6 h-6 text-white"></i>
            </button>
            
            <!-- Media Section -->
            <div class="relative w-full h-[400px] bg-gray-800">
                <?php if ($project['media_type'] === 'youtube' && !empty($project['media_url'])): ?>
                    <!-- YouTube Embed -->
                    <iframe 
                        src="https://www.youtube.com/embed/<?= htmlspecialchars($project['media_url']) ?>?autoplay=1&mute=1" 
                        class="w-full h-full"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                    
                <?php elseif ($project['media_type'] === 'gif' && !empty($project['media_url'])): ?>
                    <!-- GIF Preview -->
                    <img src="<?= htmlspecialchars($project['media_url']) ?>" 
                        alt="<?= htmlspecialchars($project['title']) ?>" 
                        class="w-full h-full object-contain">
                    
                <?php else: ?>
                    <!-- Fallback to main image -->
                    <img src="<?= htmlspecialchars($project['image_url']) ?>" 
                        alt="<?= htmlspecialchars($project['title']) ?>" 
                        class="w-full h-full object-cover">
                <?php endif; ?>
            </div>
            
            <!-- Content Section -->
            <div class="p-8">
                
                <!-- Title & Year -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-4xl font-bold text-white mb-2 font-orbitron">
                            <?= htmlspecialchars($project['title']) ?>
                        </h2>
                        <?php if ($project['is_featured']): ?>
                            <span class="inline-flex items-center px-3 py-1 bg-yellow-900 text-yellow-300 rounded-full text-sm">
                                <i data-feather="star" class="w-4 h-4 mr-1"></i> Featured Project
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="text-2xl text-gray-400 font-bold"><?= $project['year'] ?></span>
                </div>
                
                <!-- Tags & Roles -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php foreach ($tags as $tag): ?>
                        <span class="px-3 py-1 bg-indigo-900 text-indigo-300 rounded-full text-sm">
                            <?= htmlspecialchars($tag) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                
                <!-- My Roles -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">My Roles</h3>
                    <div class="flex flex-wrap gap-3">
                        <?php foreach ($roles as $role): ?>
                            <span class="px-4 py-2 bg-gray-800 rounded-lg flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full <?= getRoleBadgeColor($role) ?>"></div>
                                <span class="text-gray-300"><?= getRoleName($role) ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">About This Project</h3>
                    <p class="text-gray-300 leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>
                </div>
                
                <!-- Tech Stack -->
                <?php if (!empty($tech_stack)): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">Tech Stack</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($tech_stack as $tech): ?>
                            <span class="px-3 py-1 bg-purple-900 bg-opacity-50 text-purple-300 rounded text-sm">
                                <?= htmlspecialchars($tech) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Features -->
                <?php if (!empty($features)): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">Key Features</h3>
                    <ul class="space-y-2">
                        <?php foreach ($features as $feature): ?>
                            <li class="flex items-start gap-2 text-gray-300">
                                <i data-feather="check-circle" class="w-5 h-5 text-indigo-400 flex-shrink-0 mt-0.5"></i>
                                <span><?= htmlspecialchars($feature) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Gallery -->
                <?php if (!empty($gallery)): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">Gallery</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($gallery as $image): ?>
                            <img src="<?= htmlspecialchars($image) ?>" 
                                alt="Screenshot" 
                                class="w-full h-40 object-cover rounded-lg cursor-pointer hover:opacity-80 transition"
                                onclick="window.open('<?= htmlspecialchars($image) ?>', '_blank')">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Links -->
                <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-800">
                    <?php if (!empty($project['project_url'])): ?>
                        <a href="<?= htmlspecialchars($project['project_url']) ?>" 
                            target="_blank"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i data-feather="external-link" class="w-5 h-5"></i>
                            View Live Project
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['github_url'])): ?>
                        <a href="<?= htmlspecialchars($project['github_url']) ?>" 
                            target="_blank"
                            class="px-6 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i data-feather="github" class="w-5 h-5"></i>
                            View on GitHub
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['demo_url'])): ?>
                        <a href="<?= htmlspecialchars($project['demo_url']) ?>" 
                            target="_blank"
                            class="px-6 py-3 bg-purple-600 hover:bg-purple-700 rounded-lg font-medium transition inline-flex items-center gap-2">
                            <i data-feather="play-circle" class="w-5 h-5"></i>
                            Play Demo
                        </a>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
</div>