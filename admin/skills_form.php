<?php
// admin/skills_form.php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$skill = null;
$isEdit = false;

// Edit mode
if (isset($_GET['id'])) {
    $isEdit = true;
    $skill = fetchOne("SELECT * FROM skills WHERE id = ?", [intval($_GET['id'])]);
    if (!$skill) {
        $_SESSION['error'] = "Skill not found!";
        header('Location: index.php#skills');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $icon = clean($_POST['icon']);
    $category = clean($_POST['category']);
    $proficiency = intval($_POST['proficiency']);
    
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($icon)) $errors[] = "Icon is required";
    if (empty($category)) $errors[] = "Category is required";
    if ($proficiency < 1 || $proficiency > 5) $errors[] = "Proficiency must be between 1-5";
    
    if (empty($errors)) {
        if ($isEdit) {
            $sql = "UPDATE skills SET name = ?, icon = ?, category = ?, proficiency = ? WHERE id = ?";
            $params = [$name, $icon, $category, $proficiency, intval($_GET['id'])];
            execute($sql, $params);
            $_SESSION['success'] = "Skill updated successfully!";
        } else {
            $sql = "INSERT INTO skills (name, icon, category, proficiency) VALUES (?, ?, ?, ?)";
            execute($sql, [$name, $icon, $category, $proficiency]);
            $_SESSION['success'] = "Skill added successfully!";
        }
        header('Location: index.php#skills');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Skill - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-900 text-white">
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-400"><?= $isEdit ? 'Edit' : 'Add New' ?> Skill</h1>
            <a href="index.php#skills" class="text-gray-300 hover:text-white">← Back to Admin</a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 max-w-2xl">
        <?php if (!empty($errors)): ?>
        <div class="bg-red-900 bg-opacity-50 border border-red-500 text-red-300 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Skill Name *</label>
                <input type="text" name="name" required 
                       value="<?= htmlspecialchars($skill['name'] ?? '') ?>"
                       placeholder="e.g. JavaScript, Unity, Photoshop"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2">
                    Feather Icon Name * 
                    <a href="https://feathericons.com/" target="_blank" class="text-blue-400 text-sm">(Browse Icons)</a>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="icon" id="iconInput" required 
                           value="<?= htmlspecialchars($skill['icon'] ?? '') ?>"
                           placeholder="e.g. code, monitor, package"
                           class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="bg-gray-700 border border-gray-600 rounded px-4 py-2 flex items-center justify-center w-16">
                        <i data-feather="code" id="iconPreview" class="text-blue-400"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm mt-1">Preview will update as you type</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Category *</label>
                <select name="category" required 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Category --</option>
                    <option value="programming" <?= ($skill['category'] ?? '') === 'programming' ? 'selected' : '' ?>>Programming</option>
                    <option value="design" <?= ($skill['category'] ?? '') === 'design' ? 'selected' : '' ?>>Design</option>
                    <option value="tools" <?= ($skill['category'] ?? '') === 'tools' ? 'selected' : '' ?>>Tools</option>
                    <option value="gamedev" <?= ($skill['category'] ?? '') === 'gamedev' ? 'selected' : '' ?>>Game Development</option>
                    <option value="web" <?= ($skill['category'] ?? '') === 'web' ? 'selected' : '' ?>>Web Development</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2">
                    Proficiency Level (1-5 Stars) * 
                    <span class="text-gray-500 text-sm">Current: <span id="profValue"><?= $skill['proficiency'] ?? 3 ?></span> ⭐</span>
                </label>
                <input type="range" name="proficiency" id="profRange" min="1" max="5" step="1"
                       value="<?= $skill['proficiency'] ?? 3 ?>"
                       class="w-full h-3 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-blue-500">
                <div class="grid grid-cols-5 gap-2 text-xs text-gray-400 mt-2">
                    <div class="text-center">
                        <div class="text-yellow-400 text-lg">⭐</div>
                        <div>Beginner</div>
                    </div>
                    <div class="text-center">
                        <div class="text-yellow-400 text-lg">⭐⭐</div>
                        <div>Learning</div>
                    </div>
                    <div class="text-center">
                        <div class="text-yellow-400 text-lg">⭐⭐⭐</div>
                        <div>Proficient</div>
                    </div>
                    <div class="text-center">
                        <div class="text-yellow-400 text-lg">⭐⭐⭐⭐</div>
                        <div>Expert</div>
                    </div>
                    <div class="text-center">
                        <div class="text-yellow-400 text-lg">⭐⭐⭐⭐⭐</div>
                        <div>Master</div>
                    </div>
                </div>
                <div class="flex justify-center gap-1 mt-4 p-4 bg-gray-700 rounded-lg" id="starPreview">
                    <!-- Stars will be rendered by JS -->
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-700 hover:bg-blue-800 text-white py-3 rounded font-medium transition">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    <?= $isEdit ? 'Update' : 'Add' ?> Skill
                </button>
                <a href="index.php#skills" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded font-medium transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        // Icon preview
        const iconInput = document.getElementById('iconInput');
        const iconPreview = document.getElementById('iconPreview');
        
        iconInput.addEventListener('input', function() {
            const iconName = this.value.trim();
            if (iconName) {
                iconPreview.setAttribute('data-feather', iconName);
                feather.replace();
            }
        });

        // Proficiency slider
        const profRange = document.getElementById('profRange');
        const profValue = document.getElementById('profValue');
        const starPreview = document.getElementById('starPreview');
        
        function updateStars(value) {
            profValue.textContent = value;
            
            // Render stars with larger size
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= value) {
                    stars += '<span class="text-4xl text-yellow-400">⭐</span>';
                } else {
                    stars += '<span class="text-4xl text-gray-700">⭐</span>';
                }
            }
            starPreview.innerHTML = stars;
            
            // Update proficiency label text
            const labels = ['', 'Beginner', 'Learning', 'Proficient', 'Expert', 'Master'];
            profValue.textContent = `${value} - ${labels[value]}`;
        }
        
        profRange.addEventListener('input', function() {
            updateStars(parseInt(this.value));
        });
        
        // Initial render
        updateStars(parseInt(profRange.value));

        // Initialize icons
        feather.replace();
    </script>
</body>
</html>