<?php
// includes/header.php
$profile = getProfile();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $profile['full_name'] ?? 'Irfan Luthfiardi Anhar' ?> | <?= $profile['title'] ?? 'Game & Web Developer' ?></title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="min-h-screen">
    <!-- Background Animation -->
    <div id="vanta-background" class="fixed top-0 left-0 w-full h-full z-0"></div>
    
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-opacity-80 backdrop-filter backdrop-blur-lg bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold font-orbitron glow-text">
                    <span class="text-indigo-500">My</span>Portfolio<span class="text-purple-500">.</span>
                </a>
                <div class="hidden md:flex space-x-8">
                    <a href="index.php?page=home" class="nav-link <?= ($page ?? 'home') === 'home' ? 'text-indigo-400' : '' ?> hover:text-indigo-400">Home</a>
                    <a href="index.php?page=about" class="nav-link <?= ($page ?? '') === 'about' ? 'text-indigo-400' : '' ?> hover:text-indigo-400">About</a>
                    <a href="index.php?page=projects" class="nav-link <?= ($page ?? '') === 'projects' ? 'text-indigo-400' : '' ?> hover:text-indigo-400">Projects</a>
                    <a href="index.php?page=contact" class="nav-link <?= ($page ?? '') === 'contact' ? 'text-indigo-400' : '' ?> hover:text-indigo-400">Contact</a>
                </div>
                <button class="md:hidden text-white" id="mobile-menu-button">
                    <i data-feather="menu"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="index.php" class="<?= isActive('home') ?> hover:text-indigo-400">Home</a>
                    <a href="index.php#about" class="<?= isActive('about') ?> hover:text-indigo-400">About</a>
                    <a href="index.php#projects" class="<?= isActive('projects') ?> hover:text-indigo-400">Projects</a>
                    <a href="index.php#contact" class="<?= isActive('contact') ?> hover:text-indigo-400">Contact</a>
                </div>
            </div>
        </div>
    </nav>