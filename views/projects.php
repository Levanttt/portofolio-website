<?php
// views/projects.php - With Modal System

// Fetch other projects (non-game)
$otherProjects = fetchAll("SELECT * FROM projects WHERE category != 'game' ORDER BY year DESC, created_at DESC");
?>

<!-- Projects Section -->
<section id="projects" class="py-20 relative z-10 bg-gradient-to-b from-gray-950 to-gray-900">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold mb-4 text-center section-title font-orbitron">
            My <span class="text-purple-400">Projects</span>
        </h2>
        <p class="text-gray-400 text-center mb-12 max-w-2xl mx-auto">
            Here's a selection of my recent work in game development and other creative projects.
        </p>
        
        <!-- Role Legend -->
        <div class="flex flex-wrap justify-center gap-4 mb-8">
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full bg-indigo-500 mr-2"></div>
                <span class="text-gray-300 text-sm">Game Design</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full bg-purple-500 mr-2"></div>
                <span class="text-gray-300 text-sm">Programming</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full bg-blue-500 mr-2"></div>
                <span class="text-gray-300 text-sm">Project Manager</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                <span class="text-gray-300 text-sm">Narrative</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full bg-pink-500 mr-2"></div>
                <span class="text-gray-300 text-sm">UI/UX Design</span>
            </div>
        </div>

        <!-- Game Projects -->
        <?php if (!empty($gameProjects)): ?>
        <h3 class="text-2xl font-bold mb-6 mt-16 text-indigo-400 font-orbitron">Game Projects</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($gameProjects as $project): 
                $tags = pgArrayToPhp($project['tags']);
                $roles = pgArrayToPhp($project['roles']);
                $projectId = 'project-' . $project['id'];
            ?>
            <div class="project-card bg-gray-800 rounded-xl overflow-hidden transition-all duration-300 cursor-pointer"
                 onclick="openProjectModal('<?= $projectId ?>')">
                <div class="relative overflow-hidden h-48 group">
                    <!-- Static Image -->
                    <img src="<?= $project['image_url'] ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?>" 
                         class="w-full h-full object-cover transition-opacity duration-300"
                         id="static-<?= $projectId ?>">
                    
                    <!-- Hover Preview (GIF/Video) -->
                    <?php if (!empty($project['media_url']) && $project['media_type'] === 'gif'): ?>
                    <img src="<?= $project['media_url'] ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?> preview" 
                         class="w-full h-full object-cover absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                         loading="lazy">
                    <?php endif; ?>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/30 to-transparent"></div>
                    
                    <!-- Play Button Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="w-16 h-16 bg-indigo-600 bg-opacity-80 rounded-full flex items-center justify-center">
                            <i data-feather="play" class="w-8 h-8 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold"><?= htmlspecialchars($project['title']) ?></h3>
                        <span class="text-gray-400 text-sm"><?= $project['year'] ?></span>
                    </div>
                    <div class="flex gap-2 mb-4 flex-wrap">
                        <?php foreach ($tags as $tag): ?>
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-900 text-indigo-300"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <?php foreach ($roles as $role): ?>
                        <div class="w-4 h-4 rounded-full <?= getRoleBadgeColor($role) ?>" title="<?= getRoleName($role) ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-gray-300 text-sm mb-4 line-clamp-3">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>
                    <span class="text-indigo-400 hover:text-indigo-300 text-sm font-medium inline-flex items-center">
                        View Details <i data-feather="arrow-right" class="ml-1 w-4 h-4"></i>
                    </span>
                </div>
            </div>

            <!-- Modal for this project -->
            <?php include __DIR__ . '/../includes/project_modal.php'; ?>
            
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p class="text-gray-400 text-center py-12">No game projects yet. Add one from admin panel!</p>
        <?php endif; ?>

        <!-- Other Projects -->
        <?php if (!empty($otherProjects)): ?>
        <h3 class="text-2xl font-bold mb-6 mt-20 text-purple-400 font-orbitron">Other Projects</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($otherProjects as $project): 
                $tags = pgArrayToPhp($project['tags']);
                $roles = pgArrayToPhp($project['roles']);
                $projectId = 'project-' . $project['id'];
            ?>
            <div class="project-card bg-gray-800 rounded-xl overflow-hidden transition-all duration-300 cursor-pointer"
                 onclick="openProjectModal('<?= $projectId ?>')">
                <div class="relative overflow-hidden h-48 group">
                    <img src="<?= $project['image_url'] ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?>" 
                         class="w-full h-full object-cover transition-opacity duration-300">
                    
                    <?php if (!empty($project['media_url']) && $project['media_type'] === 'gif'): ?>
                    <img src="<?= $project['media_url'] ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?> preview" 
                         class="w-full h-full object-cover absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                         loading="lazy">
                    <?php endif; ?>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/30 to-transparent"></div>
                    
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="w-16 h-16 bg-purple-600 bg-opacity-80 rounded-full flex items-center justify-center">
                            <i data-feather="eye" class="w-8 h-8 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold"><?= htmlspecialchars($project['title']) ?></h3>
                        <span class="text-gray-400 text-sm"><?= $project['year'] ?></span>
                    </div>
                    <div class="flex gap-2 mb-4 flex-wrap">
                        <?php foreach ($tags as $tag): ?>
                        <span class="px-2 py-1 text-xs rounded-full bg-purple-900 text-purple-300"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <?php foreach ($roles as $role): ?>
                        <div class="w-4 h-4 rounded-full <?= getRoleBadgeColor($role) ?>" title="<?= getRoleName($role) ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-gray-300 text-sm mb-4 line-clamp-3">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>
                    <span class="text-indigo-400 hover:text-indigo-300 text-sm font-medium inline-flex items-center">
                        View Details <i data-feather="arrow-right" class="ml-1 w-4 h-4"></i>
                    </span>
                </div>
            </div>

            <!-- Modal for this project -->
            <?php include __DIR__ . '/../includes/project_modal.php'; ?>
            
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Script -->
<script>
function openProjectModal(projectId) {
    const modal = document.getElementById('modal-' + projectId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Reinitialize feather icons in modal
        feather.replace();
    }
}

function closeProjectModal(projectId) {
    const modal = document.getElementById('modal-' + projectId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="modal-project-"]').forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.style.overflow = 'auto';
    }
});
</script>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>