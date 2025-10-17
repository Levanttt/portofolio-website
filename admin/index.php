<?php
// admin/index.php – Secure Admin Panel with Sortable Projects
session_start();
require_once __DIR__ . '/../includes/functions.php';

// ========== CONFIGURATION ==========
$ADMIN_PASSWORD_HASH = '$2a$12$PzAMGKwLw8P5LyWnY56KneaSLLphaa0J3peOyQ9uqXYG8l/Fs043q';

// ========== AUTH HANDLING ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $password = trim($_POST['password'] ?? '');
    if (password_verify($password, $ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Wrong password!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ========== ACCESS PROTECTION ==========
if (!isset($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Admin Login</h2>
        <?php if (isset($error)): ?>
            <div class="bg-red-900 bg-opacity-50 border border-red-500 text-red-300 px-4 py-3 rounded mb-4 text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-300 mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" name="login" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-medium transition">
                Login
            </button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// ========== POST ACTIONS ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_project']) && isset($_POST['project_id'])) {
        execute("DELETE FROM projects WHERE id = ?", [intval($_POST['project_id'])]);
        $_SESSION['success'] = "Project deleted successfully!";
        header('Location: index.php');
        exit;
    }
    
    if (isset($_POST['delete_message']) && isset($_POST['message_id'])) {
        execute("DELETE FROM contact_messages WHERE id = ?", [intval($_POST['message_id'])]);
        $_SESSION['success'] = "Message deleted successfully!";
        header('Location: index.php');
        exit;
    }

    if (isset($_POST['delete_skill']) && isset($_POST['skill_id'])) {
        execute("DELETE FROM skills WHERE id = ?", [intval($_POST['skill_id'])]);
        $_SESSION['success'] = "Skill deleted successfully!";
        header('Location: index.php#skills');
        exit;
    }
    
    if (isset($_POST['mark_read']) && isset($_POST['message_id'])) {
        execute("UPDATE contact_messages SET is_read = true WHERE id = ?", [intval($_POST['message_id'])]);
        header('Location: index.php');
        exit;
    }
}

// ========== DATA FETCHING ==========
$projects = getProjects(); // Now sorted by display_order
$messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 50");
$unreadCount = fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = false")['count'] ?? 0;
$stats = [
    'total_projects' => fetchOne("SELECT COUNT(*) as count FROM projects")['count'] ?? 0,
    'total_messages' => fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0,
    'unread_messages' => $unreadCount,
    'total_skills' => fetchOne("SELECT COUNT(*) as count FROM skills")['count'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- SortableJS for Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #4f46e5 !important;
        }
        .sortable-drag {
            opacity: 1;
            cursor: grabbing !important;
        }
        .drag-handle {
            cursor: grab;
            user-select: none;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-400">Admin Panel</h1>
            <div class="flex gap-4 items-center">
                <a href="../public/index.php" class="text-gray-300 hover:text-white">View Site</a>
                <a href="?logout" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-900 bg-opacity-50 border border-green-500 text-green-300 px-4 py-3 rounded mb-6">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <!-- Toast Notification for Sort Success -->
        <div id="sortToast" class="hidden fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <span id="sortToastMessage">Order updated successfully!</span>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $cards = [
                ['label'=>'Total Projects','value'=>$stats['total_projects'],'color'=>'indigo','icon'=>'folder'],
                ['label'=>'Total Skills','value'=>$stats['total_skills'],'color'=>'purple','icon'=>'award'],
                ['label'=>'Total Messages','value'=>$stats['total_messages'],'color'=>'blue','icon'=>'mail'],
                ['label'=>'Unread Messages','value'=>$stats['unread_messages'],'color'=>'yellow','icon'=>'alert-circle'],
            ];
            foreach ($cards as $card): ?>
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 flex justify-between items-center">
                <div>
                    <p class="text-gray-400 text-sm"><?= $card['label'] ?></p>
                    <p class="text-3xl font-bold text-<?= $card['color'] ?>-400"><?= $card['value'] ?></p>
                </div>
                <i data-feather="<?= $card['icon'] ?>" class="text-<?= $card['color'] ?>-400"></i>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- NAV TABS -->
        <div class="border-b border-gray-700 mb-6">
            <nav class="flex gap-4">
                <button onclick="showTab('projects')" id="tab-projects" 
                        class="tab-button px-4 py-2 border-b-2 border-indigo-500 text-indigo-400 font-medium">
                    Projects
                </button>
                <button onclick="showTab('messages')" id="tab-messages" 
                        class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-400 hover:text-white">
                    Messages <?php if ($unreadCount > 0): ?>
                        <span class="bg-red-600 px-2 py-1 rounded-full text-xs ml-2"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </button>
                <button onclick="showTab('skills')" id="tab-skills" 
                        class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-400 hover:text-white">
                    Skills
                </button>
            </nav>
        </div>

        <!-- PROJECTS TAB -->
        <div id="content-projects" class="tab-content">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold">Projects</h2>
                        <p class="text-gray-400 text-sm mt-1">
                            <i data-feather="move" class="w-4 h-4 inline"></i>
                            Drag rows to reorder
                        </p>
                    </div>
                    <a href="project_form.php" 
                        class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded inline-block">+ Add Project</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-2 w-8"></th>
                                <th class="text-left py-3 px-4">Title</th>
                                <th class="text-left py-3 px-4">Category</th>
                                <th class="text-left py-3 px-4">Year</th>
                                <th class="text-left py-3 px-4">Featured</th>
                                <th class="text-left py-3 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-projects">
                            <?php foreach ($projects as $project): ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-750 transition" data-id="<?= $project['id'] ?>">
                                <td class="py-3 px-2">
                                    <div class="drag-handle text-gray-500 hover:text-gray-300">
                                        <i data-feather="menu" class="w-5 h-5"></i>
                                    </div>
                                </td>
                                <td class="py-3 px-4 flex items-center gap-3">
                                    <img src="../public/<?= htmlspecialchars($project['image_url']) ?>" alt="" class="w-12 h-12 rounded object-cover">
                                    <span class="font-medium"><?= htmlspecialchars($project['title']) ?></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 bg-indigo-900 text-indigo-300 rounded text-sm">
                                        <?= ucfirst(htmlspecialchars($project['category'])) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4"><?= htmlspecialchars($project['year']) ?></td>
                                <td class="py-3 px-4">
                                    <?= $project['is_featured'] ? '<span class="text-yellow-400">★ Featured</span>' : '<span class="text-gray-500">-</span>' ?>
                                </td>
                                <td class="py-3 px-4 flex gap-2">
                                    <a href="project_form.php?id=<?= intval($project['id']) ?>" 
                                        class="text-blue-400 hover:text-blue-300">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" onsubmit="return confirm('Delete this project?')">
                                        <input type="hidden" name="project_id" value="<?= intval($project['id']) ?>">
                                        <button type="submit" name="delete_project" class="text-red-400 hover:text-red-300">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($projects)): ?>
                                <tr><td colspan="6" class="text-center py-6 text-gray-400">No projects yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MESSAGES TAB -->
        <div id="content-messages" class="tab-content hidden">
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-2xl font-bold mb-6">Contact Messages</h2>
                <?php foreach ($messages as $msg): ?>
                <div class="bg-gray-750 border border-gray-700 rounded-lg p-4 mb-4 <?= $msg['is_read'] ? 'opacity-60' : '' ?>">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-lg"><?= htmlspecialchars($msg['name']) ?></h3>
                            <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="text-indigo-400 text-sm hover:text-indigo-300"><?= htmlspecialchars($msg['email']) ?></a>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <?= date('M d, Y H:i', strtotime($msg['created_at'])) ?>
                            <?php if (!$msg['is_read']): ?>
                            <span class="bg-blue-600 px-2 py-1 rounded text-xs">NEW</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="text-gray-300 mb-4"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    <div class="flex gap-2">
                        <?php if (!$msg['is_read']): ?>
                        <form method="POST">
                            <input type="hidden" name="message_id" value="<?= intval($msg['id']) ?>">
                            <button type="submit" name="mark_read" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                                <i data-feather="check" class="w-4 h-4"></i> Mark as Read
                            </button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" onsubmit="return confirm('Delete this message?')">
                            <input type="hidden" name="message_id" value="<?= intval($msg['id']) ?>">
                            <button type="submit" name="delete_message" class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1">
                                <i data-feather="trash-2" class="w-4 h-4"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($messages)): ?>
                    <p class="text-gray-400 text-center py-8">No messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

   <!-- SKILLS TAB -->
    <div id="content-skills" class="tab-content hidden">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Skills Management</h2>
                <a href="skills_form.php" 
                    class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded inline-block">+ Add Skill</a>
            </div>
            
            <?php 
            $skills = getSkills();
            $skillsByCategory = [];
            foreach ($skills as $skill) {
                $skillsByCategory[$skill['category']][] = $skill;
            }
            ?>
            
            <?php if (!empty($skillsByCategory)): ?>
                <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-blue-400 mb-4 capitalize flex items-center gap-2">
                        <i data-feather="folder" class="w-5 h-5"></i>
                        <?= ucfirst($category) ?>
                        <span class="text-sm text-gray-500">(<?= count($categorySkills) ?> skills)</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($categorySkills as $skill): ?>
                        <div class="bg-gray-750 border border-gray-700 rounded-lg p-4 hover:border-blue-500 transition">
                            <div class="flex items-start gap-3">
                                <div class="bg-gray-800 p-3 rounded-lg">
                                    <i data-feather="<?= htmlspecialchars($skill['icon']) ?>" 
                                    class="w-6 h-6 text-blue-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-white"><?= htmlspecialchars($skill['name']) ?></h4>
                                    <div class="mt-2 flex items-center gap-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= $skill['proficiency'] ? 'text-yellow-400' : 'text-gray-600' ?>">⭐</span>
                                        <?php endfor; ?>
                                        <span class="text-xs text-gray-500 ml-2">(<?= $skill['proficiency'] ?>/5)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-700">
                                <a href="skills_form.php?id=<?= $skill['id'] ?>" 
                                class="flex-1 text-center text-blue-400 hover:text-blue-300 text-sm py-2 bg-gray-800 rounded">
                                    <i data-feather="edit-2" class="w-4 h-4 inline"></i> Edit
                                </a>
                                <form method="POST" onsubmit="return confirm('Delete this skill?')" class="flex-1">
                                    <input type="hidden" name="skill_id" value="<?= $skill['id'] ?>">
                                    <button type="submit" name="delete_skill" 
                                            class="w-full text-red-400 hover:text-red-300 text-sm py-2 bg-gray-800 rounded">
                                        <i data-feather="trash-2" class="w-4 h-4 inline"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-400 text-center py-8">No skills yet. Add your first skill!</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        
        // Tab switching
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-400');
                el.classList.add('border-transparent', 'text-gray-400');
            });
            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-400');
            
            // Update URL hash
            window.location.hash = tab;
            
            // Refresh feather icons
            feather.replace();
        }

        window.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.substring(1);
            if (hash && ['projects', 'messages', 'skills'].includes(hash)) {
                showTab(hash);
            }
        });

        // Initialize Sortable for projects
        const el = document.getElementById('sortable-projects');
        if (el) {
            const sortable = new Sortable(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    // Get new order
                    const rows = document.querySelectorAll('#sortable-projects tr[data-id]');
                    const order = Array.from(rows).map(row => row.getAttribute('data-id'));
                    
                    console.log('Sending order:', order); // Debug
                    
                    // Send to server
                    fetch('update_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => {
                        // Log raw response for debugging
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers.get('content-type'));
                        
                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            return response.text().then(text => {
                                console.error('Non-JSON response:', text);
                                throw new Error('Server returned non-JSON response. Check PHP errors.');
                            });
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data); // Debug
                        
                        if (data.success) {
                            showToast('✓ Order updated successfully! (' + data.updated_count + ' items)', 'success');
                        } else {
                            showToast('✗ Failed: ' + (data.error || 'Unknown error'), 'error');
                            console.error('Server error:', data.error);
                        }
                    })
                    .catch(error => {
                        showToast('✗ Network error: ' + error.message, 'error');
                        console.error('Fetch error:', error);
                    });
                }
            });
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('sortToast');
            const toastMessage = document.getElementById('sortToastMessage');
            
            toastMessage.textContent = message;
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            } text-white`;
            
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Initialize feather icons
        feather.replace();
    </script>
</body>
</html>