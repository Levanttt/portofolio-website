<?php
// includes/functions.php

require_once __DIR__ . '/../config/database.php';

// Get profile data with image path validation
function getProfile() {
    $profile = fetchOne("SELECT * FROM profile WHERE id = 1");
    
    // Validate and fix image path
    if (!empty($profile['profile_image'])) {
        $imagePath = str_replace('\\', '/', $profile['profile_image']);
        
        // Remove 'public/' prefix if exists
        $imagePath = preg_replace('#^/?public/#', '', $imagePath);
        
        // Check if it's a remote URL
        if (strpos($imagePath, 'http') !== 0) {
            // Build full file path for validation
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/Portofolio/public/' . ltrim($imagePath, '/');
            
            // If file doesn't exist, clear the image path
            if (!file_exists($fullPath)) {
                $profile['profile_image'] = '';
            } else {
                $profile['profile_image'] = $imagePath;
            }
        }
    }
    
    return $profile;
}

// Get all projects or filtered by category
function getProjects($category = null, $featured = null, $orderBy = null) {
    $sql = "SELECT * FROM projects WHERE 1=1";
    $params = [];

    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if ($featured !== null) {
        $sql .= " AND is_featured = ?";
        $params[] = $featured;
    }

    // Fix utama: pastikan display_order jadi prioritas utama urutan tampilan
    if ($orderBy === 'display_order' || $orderBy === null) {
        $sql .= " ORDER BY display_order ASC, created_at DESC";
    } else {
        $sql .= " ORDER BY year DESC, created_at DESC";
    }

    return fetchAll($sql, $params);
}

// Get a single project by its ID
function getProject($id) {
    return fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
}

// Get skills by category
function getSkills($category = null) {
    if ($category) {
        return fetchAll("SELECT * FROM skills WHERE category = ? ORDER BY proficiency DESC, name", [$category]);
    }
    return fetchAll("SELECT * FROM skills ORDER BY category, proficiency DESC, name");
}

// Get skills grouped by category
function getSkillsByCategory() {
    $skills = getSkills();
    $grouped = [];
    
    foreach ($skills as $skill) {
        $grouped[$skill['category']][] = $skill;
    }
    
    return $grouped;
}

// Save contact message
function saveContactMessage($name, $email, $message) {
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    return execute($sql, [$name, $email, $message]);
}

// Get role badge color with Navy theme
function getRoleBadgeColor($role) {
    $colors = [
        'game_design' => 'bg-blue-800',         // Navy Blue (Dominan)
        'programming' => 'bg-cyan-500',         // Cyan (Accent)
        'project_manager' => 'bg-blue-700',     // Dark Blue      
        'web_developer' => 'bg-teal-500',       // Teal
        'ui_ux' => 'bg-sky-500'                 // Sky Blue
    ];
    
    return $colors[$role] ?? 'bg-gray-500';
}

// Get role name
function getRoleName($role) {
    $names = [
        'game_design' => 'Game Design',
        'programming' => 'Programming',
        'project_manager' => 'Project Manager',  
        'web_developer' => 'Web Developer',      
        'ui_ux' => 'UI/UX Design'
    ];
    
    return $names[$role] ?? ucfirst(str_replace('_', ' ', $role));
}

// Clean and sanitize input
function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Format PostgreSQL array to PHP array
function pgArrayToPhp($pgArray) {
    if (empty($pgArray) || $pgArray === '{}' || $pgArray === 'NULL') {
        return [];
    }
    
    // Remove outer braces
    $pgArray = trim($pgArray, '{}');
    
    // Split by comma, but respect quoted strings
    $result = [];
    $current = '';
    $inQuotes = false;
    $len = strlen($pgArray);
    
    for ($i = 0; $i < $len; $i++) {
        $char = $pgArray[$i];
        
        if ($char === '"' && ($i === 0 || $pgArray[$i-1] !== '\\')) {
            $inQuotes = !$inQuotes;
        } elseif ($char === ',' && !$inQuotes) {
            $result[] = trim($current, ' "');
            $current = '';
        } else {
            $current .= $char;
        }
    }
    
    // Add last item
    if ($current !== '') {
        $result[] = trim($current, ' "');
    }
    
    return array_filter($result);
}

// Get current page
function getCurrentPage() {
    $page = $_GET['page'] ?? 'home';
    $allowedPages = ['home', 'about', 'projects', 'contact'];
    
    return in_array($page, $allowedPages) ? $page : 'home';
}

// Check if page is active (Navy theme)
function isActive($page) {
    return getCurrentPage() === $page ? 'text-blue-400' : 'text-white';
}
?>