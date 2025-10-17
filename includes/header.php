<?php
// includes/header.php
$profile = getProfile();

// Deteksi halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Jika sedang di halaman modal project, hide navbar
$hide_nav = ($current_page === 'project_modal.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $profile['full_name'] ?? 'Irfan Luthfiardi Anhar' ?> | <?= $profile['title'] ?? 'Game & Web Developer' ?></title>
    
    <!-- Tailwind CSS - Production Build -->
    <link rel="stylesheet" href="assets/css/tailwind.css">
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Three.js & Vanta.js for background -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎮</text></svg>">
    
    <!-- Custom JS -->
    <script src="assets/js/gallery.js" defer></script>
</head>
<body class="min-h-screen">
    <!-- Background Animation -->
    <div id="vanta-background" class="fixed top-0 left-0 w-full h-full z-0"></div>

    <?php if (!$hide_nav): ?>
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-opacity-80 backdrop-filter backdrop-blur-lg bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-2xl font-bold font-orbitron glow-text">
                    <span class="text-blue-400">My</span>Portfolio<span class="text-blue-400">.</span>
                </a>
                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="nav-link <?= isActive('home') ?> hover:text-blue-400">Home</a>
                    <a href="index.php#about" class="nav-link <?= isActive('about') ?> hover:text-blue-400">About</a>
                    <a href="index.php#projects" class="nav-link <?= isActive('projects') ?> hover:text-blue-400">Projects</a>
                    <a href="index.php#contact" class="nav-link <?= isActive('contact') ?> hover:text-blue-400">Contact</a>
                </div>
                <button class="md:hidden text-white" id="mobile-menu-button">
                    <i data-feather="menu"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="index.php" class="<?= isActive('home') ?> hover:text-blue-400">Home</a>
                    <a href="index.php#about" class="<?= isActive('about') ?> hover:text-blue-400">About</a>
                    <a href="index.php#projects" class="<?= isActive('projects') ?> hover:text-blue-400">Projects</a>
                    <a href="index.php#contact" class="<?= isActive('contact') ?> hover:text-blue-400">Contact</a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>