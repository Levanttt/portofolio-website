<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Get project data for editing
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
    $github_url = clean($_POST['github_url'] ?? '');
    $demo_url = clean($_POST['demo_url'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 't' : 'f'; 
    
    // Media type and URL
    $media_type = clean($_POST['media_type'] ?? 'image');
    $media_url = clean($_POST['media_url'] ?? '');
    
    // Tags, roles, tech_stack, features (multiple)
    $tags = $_POST['tags'] ?? [];
    $roles = $_POST['roles'] ?? [];
    $tech_stack = isset($_POST['tech_stack']) ? array_filter(array_map('trim', explode(',', $_POST['tech_stack']))) : [];
    $features = isset($_POST['features']) ? array_filter(array_map('trim', explode("\n", $_POST['features']))) : [];
    
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

        echo "Filename: " . $filename . "<br>";
        echo "Extension: " . $ext . "<br>";
        echo "Upload path: " . __DIR__ . '/../public/images/' . "<br>";
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image format. Allowed: jpg, jpeg, png, gif, webp";
        } else {
            $newFilename = uniqid() . '_' . time() . '.' . $ext;
            $uploadPath = __DIR__ . '/../public/images/' . $newFilename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image_url = 'images/' . $newFilename;
                
                // Delete old image if editing
                if ($isEdit && !empty($project['image_url']) && file_exists(__DIR__ . '/../public/' . $project['image_url'])) {
                    @unlink(__DIR__ . '/../public/' . $project['image_url']);
                }
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Convert arrays to PostgreSQL array format (FIXED)
        $tagsArray = empty($tags) ? null : '{' . implode(',', array_map(function($tag) {
            return '"' . str_replace(['"', '\\'], ['\"', '\\\\'], trim($tag)) . '"';
        }, $tags)) . '}';

        $rolesArray = empty($roles) ? null : '{' . implode(',', $roles) . '}';

        $techStackArray = empty($tech_stack) ? null : '{' . implode(',', array_map(function($tech) {
            return '"' . str_replace(['"', '\\'], ['\"', '\\\\'], trim($tech)) . '"';
        }, $tech_stack)) . '}';

        $featuresArray = empty($features) ? null : '{' . implode(',', array_map(function($feat) {
            return '"' . str_replace(['"', '\\'], ['\"', '\\\\'], trim($feat)) . '"';
        }, $features)) . '}';

        $galleryArray = null; // Set null instead of empty array
        
        if ($isEdit) {
            // Update existing project
            $sql = "UPDATE projects SET 
                    title = ?, description = ?, year = ?, category = ?,
                    tags = ?, roles = ?, image_url = ?, project_url = ?, 
                    github_url = ?, demo_url = ?, is_featured = ?,
                    media_type = ?, media_url = ?, tech_stack = ?, features = ?,
                    updated_at = NOW()
                    WHERE id = ?";
            $params = [$title, $description, $year, $category, $tagsArray, $rolesArray, 
                      $image_url, $project_url, $github_url, $demo_url, $is_featured,
                      $media_type, $media_url, $techStackArray, $featuresArray, $_GET['id']];
            
            if (execute($sql, $params)) {
                $_SESSION['success'] = "Project updated successfully!";
                header('Location: index.php');
                exit;
            } else {
                $errors[] = "Failed to update project";
            }
        } else {
            // Insert new project
            $sql = "INSERT INTO projects (title, description, year, category, tags, roles, image_url, project_url, 
                    github_url, demo_url, is_featured, media_type, media_url, tech_stack, features, gallery_images)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$title, $description, $year, $category, $tagsArray, $rolesArray, 
                      $image_url, $project_url, $github_url, $demo_url, $is_featured,
                      $media_type, $media_url, $techStackArray, $featuresArray, $galleryArray];
            
                    
            if (execute($sql, $params)) {
                $_SESSION['success'] = "Project created successfully!";
                header('Location: index.php');
                exit;
            } else {
                // Show detailed error
                $errors[] = "Failed to create project. Check database logs.";
                error_log("SQL Error: " . print_r($params, true));
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
    'github_url' => $project['github_url'] ?? '',
    'demo_url' => $project['demo_url'] ?? '',
    'is_featured' => $project['is_featured'] ?? false,
    'tags' => $isEdit ? pgArrayToPhp($project['tags']) : [],
    'roles' => $isEdit ? pgArrayToPhp($project['roles']) : [],
    'tech_stack' => $isEdit && !empty($project['tech_stack']) ? implode(', ', pgArrayToPhp($project['tech_stack'])) : '',
    'features' => $isEdit && !empty($project['features']) ? implode("\n", pgArrayToPhp($project['features'])) : '',
    'image_url' => $project['image_url'] ?? '',
    'media_type' => $project['media_type'] ?? 'image',
    'media_url' => $project['media_url'] ?? ''
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
                        <option value="game" <?= $formData['category'] === 'game' ? 'selected' : '' ?>>Game Project</option>
                        <option value="other" <?= $formData['category'] === 'other' ? 'selected' : '' ?>>Other Project</option>
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
                        'project_manager' => 'Project Manager',
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
                <label class="block text-gray-300 mb-2 font-medium">Project Thumbnail <?= $isEdit ? '' : '*' ?></label>
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

            <!-- Media Type Selection -->
            <div class="mb-6 bg-gray-700 p-6 rounded-lg border-2 border-indigo-600">
                <label class="block text-gray-300 mb-3 font-medium text-lg">🎬 Preview Media (for hover & modal)</label>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Media Type:</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="media_type" value="image" 
                                   <?= $formData['media_type'] === 'image' ? 'checked' : '' ?>
                                   onchange="toggleMediaInput()"
                                   class="w-4 h-4 text-indigo-600">
                            <span>📷 Image Only (No Preview)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="media_type" value="gif" 
                                   <?= $formData['media_type'] === 'gif' ? 'checked' : '' ?>
                                   onchange="toggleMediaInput()"
                                   class="w-4 h-4 text-indigo-600">
                            <span>🎞️ GIF Animation</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="media_type" value="youtube" 
                                   <?= $formData['media_type'] === 'youtube' ? 'checked' : '' ?>
                                   onchange="toggleMediaInput()"
                                   class="w-4 h-4 text-indigo-600">
                            <span>▶️ YouTube Video</span>
                        </label>
                    </div>
                </div>

                <div id="mediaUrlInput" class="<?= $formData['media_type'] === 'image' ? 'hidden' : '' ?>">
                    <label class="block text-gray-300 mb-2">
                        <span id="mediaLabel">Media URL:</span>
                    </label>
                    <input type="text" name="media_url" id="media_url"
                           value="<?= htmlspecialchars($formData['media_url']) ?>"
                           class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Enter URL...">
                    <p class="text-gray-400 text-sm mt-2" id="mediaHelp">
                        <!-- Dynamic help text -->
                    </p>
                </div>
            </div>

            <!-- Project URL -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Project URL (Optional)</label>
                <input type="url" name="project_url" 
                       value="<?= htmlspecialchars($formData['project_url']) ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="https://...">
                <p class="text-gray-400 text-sm mt-1">Link to itch.io, Steam, website, etc.</p>
            </div>

            <!-- GitHub & Demo URLs -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">GitHub URL (Optional)</label>
                    <input type="url" name="github_url" 
                           value="<?= htmlspecialchars($formData['github_url']) ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="https://github.com/...">
                </div>
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Demo/Download URL (Optional)</label>
                    <input type="url" name="demo_url" 
                           value="<?= htmlspecialchars($formData['demo_url']) ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="https://...">
                </div>
            </div>

            <!-- Tech Stack -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Tech Stack (Optional)</label>
                <input type="text" name="tech_stack" 
                       value="<?= htmlspecialchars($formData['tech_stack']) ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Unity, C#, Blender, Photoshop">
                <p class="text-gray-400 text-sm mt-1">Separate with commas</p>
            </div>

            <!-- Features -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-medium">Key Features (Optional)</label>
                <textarea name="features" rows="6"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="One feature per line&#10;Dynamic weather system&#10;AI-powered NPCs&#10;Procedurally generated levels"><?= htmlspecialchars($formData['features']) ?></textarea>
                <p class="text-gray-400 text-sm mt-1">One feature per line</p>
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
        
        // Tag management
        let selectedTags = <?= json_encode($formData['tags']) ?>;
        
        function renderTags() {
            const container = document.getElementById('selectedTagsList');
            const inputsContainer = document.getElementById('tagInputs');
            
            container.innerHTML = '';
            inputsContainer.innerHTML = '';
            
            if (selectedTags.length === 0) {
                container.innerHTML = '<span class="text-gray-500 text-sm">No tags selected yet</span>';
                return;
            }
            
            selectedTags.forEach((tag, index) => {
                const badge = document.createElement('span');
                badge.className = 'px-3 py-1 bg-indigo-600 text-white rounded-full text-sm flex items-center gap-2';
                badge.innerHTML = tag + ' <button type="button" onclick="removeTag(' + index + ')" class="hover:text-red-300 text-lg">×</button>';
                container.appendChild(badge);
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tags[]';
                input.value = tag;
                inputsContainer.appendChild(input);
            });
        }
        
        function addTag(tag) {
            if (!selectedTags.includes(tag)) {
                selectedTags.push(tag);
                renderTags();
            }
        }
        
        function addCustomTag() {
            const input = document.getElementById('customTagInput');
            const tag = input.value.trim();
            
            if (tag && !selectedTags.includes(tag)) {
                selectedTags.push(tag);
                renderTags();
                input.value = '';
            }
        }
        
        function removeTag(index) {
            selectedTags.splice(index, 1);
            renderTags();
        }
        
        document.getElementById('customTagInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomTag();
            }
        });
        
        renderTags();
        
        // Media type toggle
        function toggleMediaInput() {
            const mediaType = document.querySelector('input[name="media_type"]:checked').value;
            const mediaUrlInput = document.getElementById('mediaUrlInput');
            const mediaLabel = document.getElementById('mediaLabel');
            const mediaHelp = document.getElementById('mediaHelp');
            const urlField = document.getElementById('media_url');
            
            if (mediaType === 'image') {
                mediaUrlInput.classList.add('hidden');
                urlField.value = '';
            } else {
                mediaUrlInput.classList.remove('hidden');
                
                if (mediaType === 'gif') {
                    mediaLabel.textContent = 'GIF URL:';
                    mediaHelp.innerHTML = 'Upload GIF to <a href="https://imgur.com" target="_blank" class="text-indigo-400 underline">Imgur</a> or paste direct GIF URL';
                    urlField.placeholder = 'https://i.imgur.com/example.gif';
                } else if (mediaType === 'youtube') {
                    mediaLabel.textContent = 'YouTube Video ID:';
                    mediaHelp.innerHTML = 'Example: If URL is <code class="bg-gray-900 px-2 py-1 rounded">youtube.com/watch?v=<strong>dQw4w9WgXcQ</strong></code>, enter: <strong>dQw4w9WgXcQ</strong>';
                    urlField.placeholder = 'dQw4w9WgXcQ';
                }
            }
        }
        
        toggleMediaInput();
        
        document.querySelector('form').addEventListener('submit', function(e) {
            if (selectedTags.length === 0) {
                e.preventDefault();
                alert('Please select at least one tag!');
                return false;
            }
            
            const mediaType = document.querySelector('input[name="media_type"]:checked').value;
            const mediaUrl = document.getElementById('media_url').value.trim();
            
            if (mediaType !== 'image' && !mediaUrl) {
                e.preventDefault();
                alert('Please enter ' + (mediaType === 'gif' ? 'GIF URL' : 'YouTube Video ID'));
                return false;
            }
        });
    </script>
</body>
</html>