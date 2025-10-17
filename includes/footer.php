<?php
// includes/footer.php
$profile = getProfile();
?>

    <!-- Footer -->
    <footer class="py-8 bg-gray-950 relative z-10">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <a href="index.php" class="text-2xl font-bold font-orbitron glow-text">
                        <span class="text-blue-400">My</span>Portfolio<span class="text-purple-500">.</span>
                    </a>
                </div>
                <div class="flex space-x-6">
                    <?php if ($profile['github_url']): ?>
                    <a href="<?= $profile['github_url'] ?>" target="_blank" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300">
                        <i data-feather="github"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($profile['linkedin_url']): ?>
                    <a href="<?= $profile['linkedin_url'] ?>" target="_blank" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300">
                        <i data-feather="linkedin"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="#" class="text-gray-400 hover:text-indigo-400 transition-colors duration-300">
                        <i data-feather="instagram"></i>
                    </a>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-800 text-center text-gray-500 text-sm">
                <p>© <?= date('Y') ?> <?= $profile['full_name'] ?>. All rights reserved.</p>
                <p class="mt-2">Built with passion, code, and lots of coffee.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>feather.replace();</script>
</body>
</html>