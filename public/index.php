<?php
// public/index.php

session_start();
require_once __DIR__ . '/../includes/functions.php';

// Get data
$profile = getProfile();
$featuredProjects = getProjects(null, true);
$gameProjects = getProjects('game');
$webProjects = getProjects('web');
$uiuxProjects = getProjects('uiux');
$skills = getSkills();

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    $errors = [];
    
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($message)) $errors[] = "Message is required";
    
    if (empty($errors)) {
        if (saveContactMessage($name, $email, $message)) {
            $_SESSION['success'] = "Message sent successfully! I'll get back to you soon.";
            header('Location: index.php#contact');
            exit;
        } else {
            $_SESSION['error'] = "Failed to send message. Please try again.";
        }
    } else {
        $_SESSION['error'] = implode(', ', $errors);
    }
}

// Routing sederhana
$page = $_GET['page'] ?? 'home';

// Validasi halaman yang ada
$validPages = ['home', 'about', 'projects', 'contact'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Include header
include __DIR__ . '/../includes/header.php';

// Include halaman yang diminta
include __DIR__ . '/../views/' . $page . '.php';

// Include footer
include __DIR__ . '/../includes/footer.php';
?>