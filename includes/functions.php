<?php
// includes/functions.php

require_once __DIR__ . '/../config/database.php';

// Get profile data
function getProfile() {
    return fetchOne("SELECT * FROM profile WHERE id = 1");
}

// Get all projects or filtered by category
function getProjects($category = null, $featured = null) {
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
    
    $sql .= " ORDER BY year DESC, created_at DESC";
    
    return fetchAll($sql, $params);
}

// Get single project
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

function getRoleBadgeColor($role) {
    $colors = [
        'game_design' => 'bg-indigo-500',
        'programming' => 'bg-purple-500',
        'project_manager' => 'bg-blue-500',
        'narrative' => 'bg-green-500',
        'design' => 'bg-pink-500'
    ];
    
    return $colors[$role] ?? 'bg-gray-500';
}

function getRoleName($role) {
    $names = [
        'game_design' => 'Game Design',
        'programming' => 'Programming',
        'project_manager' => 'Project Manager',
        'narrative' => 'Narrative',
        'design' => 'Design'
    ];
    
    return $names[$role] ?? $role;
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

// Check if page is active
function isActive($page) {
    return getCurrentPage() === $page ? 'text-indigo-400' : 'text-white';
}
?>