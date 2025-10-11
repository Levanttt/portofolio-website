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

// Get role badge color
function getRoleBadgeColor($role) {
    $colors = [
        'game_design' => 'bg-indigo-500',
        'programming' => 'bg-purple-500',
        'technical_art' => 'bg-blue-500',
        'narrative' => 'bg-green-500',
        'design' => 'bg-pink-500'
    ];
    
    return $colors[$role] ?? 'bg-gray-500';
}

// Get role name
function getRoleName($role) {
    $names = [
        'game_design' => 'Game Design',
        'programming' => 'Programming',
        'technical_art' => 'Technical Art',
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
    if (empty($pgArray) || $pgArray === '{}') {
        return [];
    }
    
    // Remove {} and split by comma
    $pgArray = trim($pgArray, '{}');
    return array_map('trim', explode(',', $pgArray));
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