<?php
// views/contact.php
?>

<!-- Contact Section -->
<section id="contact" class="py-20 relative z-10 bg-gray-900">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-bold mb-12 text-center section-title font-orbitron">
            Get In <span class="text-blue-400">Touch</span>
        </h2>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="max-w-2xl mx-auto mb-6 p-4 bg-green-900 bg-opacity-50 border border-green-500 rounded-lg text-green-300">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-2xl mx-auto mb-6 p-4 bg-red-900 bg-opacity-50 border border-red-500 rounded-lg text-red-300">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        
        <div class="flex flex-col lg:flex-row gap-12">
            <div class="lg:w-1/2">
                <h3 class="text-2xl font-bold mb-6 text-blue-700 font-orbitron">Contact Form</h3>
                <form method="POST" action="index.php#contact" class="space-y-6">
                    <div>
                        <label for="name" class="block text-gray-300 mb-2">Your Name</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 text-white transition-all duration-300">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-300 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 text-white transition-all duration-300">
                    </div>
                    <div>
                        <label for="message" class="block text-gray-300 mb-2">Your Message</label>
                        <textarea id="message" name="message" rows="5" required class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700 text-white transition-all duration-300"></textarea>
                    </div>
                    <button type="submit" name="contact_submit" class="px-8 py-3 bg-blue-800 hover:bg-blue-900 rounded-lg font-medium text-white transition-all duration-300 transform hover:scale-105 glow-box">
                        Send Message
                    </button>
                </form>
            </div>
            
            <div class="lg:w-1/2">
                <h3 class="text-2xl font-bold mb-6 text-blue-400 font-orbitron">Connect With Me</h3>
                <p class="text-gray-300 mb-8">
                    Interested in collaborating on a game project or have questions about my work? Feel free to reach out through the form or connect with me on social media.
                </p>
                
                <div class="space-y-4">
                    <?php if ($profile['email']): ?>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-800 flex items-center justify-center mr-4 glow-box">
                            <i data-feather="mail" class="text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm">Email</h4>
                            <a href="mailto:<?= $profile['email'] ?>" class="text-white hover:text-blue-400 transition-colors duration-300"><?= $profile['email'] ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($profile['github_url']): ?>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-800 flex items-center justify-center mr-4 glow-box">
                            <i data-feather="github" class="text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm">GitHub</h4>
                            <a href="<?= $profile['github_url'] ?>" target="_blank" class="text-white hover:text-blue-400 transition-colors duration-300"><?= $profile['github_url'] ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($profile['linkedin_url']): ?>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-800 flex items-center justify-center mr-4 glow-box">
                            <i data-feather="linkedin" class="text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm">LinkedIn</h4>
                            <a href="<?= $profile['linkedin_url'] ?>" target="_blank" class="text-white hover:text-blue-400 transition-colors duration-300"><?= $profile['linkedin_url'] ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($profile['itch_url']): ?>
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-gray-800 flex items-center justify-center mr-4 glow-box">
                            <i data-feather="globe" class="text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-sm">Itch.io</h4>
                            <a href="<?= $profile['itch_url'] ?>" target="_blank" class="text-white hover:text-blue-400 transition-colors duration-300"><?= $profile['itch_url'] ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-12">
                    <h4 class="text-xl font-bold mb-4 text-white font-orbitron">Currently Available For</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-900 bg-opacity-50 rounded-full text-blue-300 text-sm">Game Development</span>
                        <span class="px-3 py-1 bg-blue-800 bg-opacity-50 rounded-full text-blue-200 text-sm">Web Development</span>
                        <span class="px-3 py-1 bg-cyan-900 bg-opacity-50 rounded-full text-cyan-300 text-sm">UI/UX Design</span>
                        <span class="px-3 py-1 bg-teal-900 bg-opacity-50 rounded-full text-teal-300 text-sm">Freelance Work</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>