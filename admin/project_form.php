<?php
// admin/project_form.php - Add/Edit Project Form
session_start();

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$isEdit = isset($_GET['id']);
$project = null;
$errors = [];

if ($isEdit) {
    $project = getProject($_GET['id']);
    if (!$project) {
        $_SESSION['error'] = "Project not found!";
        header('Location: index.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $title = clean($_POST['title'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $year = intval($_POST['year'] ?? date('Y'));
    $category = clean($_POST['category'] ?? '');
    $project_url = clean($_POST['project_url'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? true : false;
    
    // Tags and roles (multiple select)
    $tags = $_POST['tags'] ?? [];
    $roles = $_POST['roles'] ?? [];
    
    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($category)) $errors[] = "Category is required";
    if (empty($tags)) $errors[] = "At least one tag is required";
    if (empty($roles)) $errors[] = "At least one role is required";
    
    // Handle image upload
    $image_url = $project['image_url'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image format. Allowed: jpg, jpeg, png, gif, webp";
        } else {
            $newFilename = uniqid() . '_' . time() . '.' . $ext;
            $uploadPath = __DIR__ . '/../public/images/' . $newFilename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image_url = 'images/' . $newFilename;
                
                // Delete old image if editing
                if ($isEdit && !empty($project['image_url']) && file_exists(__DIR__ . '/../public/' . $project['image_url'])) {
                    unlink(__DIR__ . '/../public/' . $project['image_url']);
                }
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Convert arrays to PostgreSQL array format
        $tagsArray = '{' . implode(',', array_map(function($tag) {
            return '"' . str_replace('"', '\"', $tag) . '"';
        }, $tags)) . '}';
        
        $rolesArray = '{' . implode(',', $roles) . '}';
        
        if ($isEdit) {
            // Update existing project
            $sql = "UPDATE projects SET 
                    title = ?, description = ?, year = ?, category = ?,
                    tags = ?, roles = ?, image_url = ?, project_url = ?, 
                    is_featured = ?, updated_at = NOW()
                    WHERE id = ?";
            $params = [$title, $description, $year, $category, $tagsArray, $rolesArray, 
                        $image_url, $project_url, $is_featured, $_GET['id']];
            
            if (execute($sql, $params)) {
                $_SESSION['success'] = "Project updated successfully!";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Failed to update project";
            }
        } else {
            // Insert new project
            $sql = "INSERT INTO projects (title, description, year, category, tags, roles, image_url, project_url, is_featured)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$title, $description, $year, $category, $tagsArray, $rolesArray, 
                        $image_url, $project_url, $is_featured];
            
            if (execute($sql, $params)) {
                $_SESSION['success'] = "Project created successfully!";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Failed to create project";
            }
        }
    }
}

// Prepare data for form
$formData = [
    'title' => $project['title'] ?? '',
    'description' => $project['description'] ?? '',
    'year' => $project['year'] ?? date('Y'),
    'category' => $project['category'] ?? '',
    'project_url' => $project['project_url'] ?? '',
    'is_featured' => $project['is_featured'] ?? false,
    'tags' => $isEdit ? pgArrayToPhp($project['tags']) : [],
    'roles' => $isEdit ? pgArrayToPhp($project['roles']) : [],
    'image_url' => $project['image_url'] ?? ''
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Project - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-900 text-white">
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-400"><?= $isEdit ? 'Edit' : 'Add New' ?> Project</h1>
            <div class="flex gap-4">
                <a href="index.php" class="text-gray-300 hover:text-white">← Back to Admin</a>
                <a href="?logout" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 max-w-4xl">
        <?php if (!empty($errors)): ?>
        <div class="bg-red-900 bg-opacity-50 border border-red-500 text-red-300 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            
            <!-- Title -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Project Title *</label>
                <input type="text" name="title" required 
                    value="<?= htmlspecialchars($formData['title']) ?>"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="e.g. Awesome Game Project">
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Description *</label>
                <textarea name="description" rows="5" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Describe your project..."><?= htmlspecialchars($formData['description']) ?></textarea>
            </div>

            <!-- Year & Category -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Year *</label>
                    <input type="number" name="year" required min="2000" max="2099"
                            value="<?= htmlspecialchars($formData['year']) ?>"
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Category *</label>
                    <select name="category" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        <option value="game" <?= $formData['category'] === 'game' ? 'selected' : '' ?>>Game</option>
                        <option value="web" <?= $formData['category'] === 'web' ? 'selected' : '' ?>>Web</option>
                        <option value="uiux" <?= $formData['category'] === 'uiux' ? 'selected' : '' ?>>UI/UX</option>
                    </select>
                </div>
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Tags *</label>
                
                <!-- Preset Tags -->
                <div class="mb-3">
                    <p class="text-gray-400 text-sm mb-2">Quick Select (Click to add):</p>
                    <div id="presetTags" class="flex flex-wrap gap-2">
                        <?php 
                        $availableTags = ['Unity', 'Unreal', 'Godot', 'C#', 'C++', 'Python', '3D', '2D', 'VR', 'AR', 
                                            'Adventure', 'Puzzle', 'Action', 'RPG', 'Strategy', 'Simulation', 'Horror',
                                            'Arcade', 'Platformer', 'Shooter', 'Mobile', 'PC', 'Console', 
                                            'Web App', 'Laravel', 'React', 'Vue', 'Figma', 'UI/UX', 'Blender'];
                        foreach ($availableTags as $tag): 
                        ?>
                            <button type="button" onclick="addTag('<?= $tag ?>')" 
                                    class="px-3 py-1 text-sm bg-gray-700 hover:bg-indigo-600 rounded-full transition">
                                <?= $tag ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Custom Tag Input -->
                <div class="mb-3">
                    <label class="text-gray-400 text-sm mb-2 block">Or Add Custom Tag:</label>
                    <div class="flex gap-2">
                        <input type="text" id="customTagInput" 
                            class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Enter custom tag...">
                        <button type="button" onclick="addCustomTag()" 
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded">
                            + Add
                        </button>
                    </div>
                </div>
                
                <!-- Selected Tags Display -->
                <div id="selectedTagsContainer" class="bg-gray-700 rounded p-3 min-h-[60px]">
                    <p class="text-gray-400 text-sm mb-2">Selected Tags:</p>
                    <div id="selectedTagsList" class="flex flex-wrap gap-2">
                        <?php if (empty($formData['tags'])): ?>
                            <span class="text-gray-500 text-sm">No tags selected yet</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Hidden inputs for form submission -->
                <div id="tagInputs"></div>
            </div>

            <!-- Roles -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Your Roles * (Select all that apply)</label>
                <div class="grid grid-cols-2 gap-3">
                    <?php 
                    $availableRoles = [
                        'game_design' => 'Game Design',
                        'programming' => 'Programming',
                        'technical_art' => 'Technical Art',
                        'narrative' => 'Narrative',
                        'design' => 'UI/UX Design'
                    ];
                    foreach ($availableRoles as $value => $label): 
                    ?>
                        <label class="flex items-center gap-2 bg-gray-700 p-3 rounded cursor-pointer hover:bg-gray-600">
                            <input type="checkbox" name="roles[]" value="<?= $value ?>"
                                <?= in_array($value, $formData['roles']) ? 'checked' : '' ?>
                                class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                            <span><?= $label ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Project Image <?= $isEdit ? '' : '*' ?></label>
                <?php if ($isEdit && !empty($formData['image_url'])): ?>
                    <div class="mb-3">
                        <img src="../public/<?= htmlspecialchars($formData['image_url']) ?>" alt="Current" 
                            class="w-48 h-32 object-cover rounded border border-gray-600">
                        <p class="text-gray-400 text-sm mt-1">Current image (upload new to replace)</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" <?= $isEdit ? '' : 'required' ?>
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-gray-400 text-sm mt-1">Supported: JPG, PNG, GIF, WEBP (Max 5MB)</p>
            </div>

            <!-- Project URL -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Project URL (Optional)</label>
                <input type="url" name="project_url" 
                    value="<?= htmlspecialchars($formData['project_url']) ?>"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="https://...">
            </div>

            <!-- Featured -->
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" 
                        <?= $formData['is_featured'] ? 'checked' : '' ?>
                        class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="text-gray-300 font-medium">⭐ Featured Project (Show on homepage)</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4">
                <button type="submit" 
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 rounded font-medium transition">
                    <?= $isEdit ? '💾 Update Project' : '➕ Create Project' ?>
                </button>
                <a href="index.php" 
                    class="px-8 py-3 bg-gray-700 hover:bg-gray-600 rounded font-medium transition inline-block">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        feather.replace();
        
        // Show selected tags
        const tagSelect = document.querySelector('select[name="tags[]"]');
        const tagDisplay = document.getElementById('selectedTags');
        
        function updateSelectedTags() {
            const selected = Array.from(tagSelect.selectedOptions).map(opt => opt.value);
            tagDisplay.textContent = selected.length > 0 ? selected.join(', ') : 'None';
        }
        
        tagSelect.addEventListener('change', updateSelectedTags);
        updateSelectedTags();
    </script>
</body>
</html>