<?php
$pageTitle = 'Privacy Policy | Bridge Ministries International';
$pageDescription = 'Our Privacy Policy and Data Collection terms.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

include 'includes/header.php';
?>

<!-- HERO SECTION -->
<div class="relative pt-32 pb-20 md:pt-48 md:pb-32 bg-[#000000] overflow-hidden">
    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
        <h1 class="text-4xl md:text-7xl font-display font-black text-white tracking-normal mb-6">
            Privacy Policy
        </h1>
        <p class="text-xl text-white/30 max-w-2xl mx-auto font-medium mb-10">
            How we protect and handle your information.
        </p>
    </div>
</div>

<div class="py-24 bg-[#F5F5F5] text-black">
    <div class="max-w-4xl mx-auto px-6 prose prose-lg">
        <h2>1. Information We Collect</h2>
        <p>We collect information from you when you fill out a form, make a donation, or subscribe to our newsletter. This includes your name, email address, and phone number.</p>
        
        <h2>2. How We Use Your Information</h2>
        <p>Any of the information we collect from you may be used in the following ways:</p>
        <ul>
            <li>To personalize your experience</li>
            <li>To process transactions securely</li>
            <li>To send periodic emails (like our newsletter or event updates)</li>
        </ul>
        
        <h2>3. Data Protection</h2>
        <p>We implement a variety of security measures to maintain the safety of your personal information when you submit a request or enter your personal information.</p>
        
        <h2>4. Cookies</h2>
        <p>We use cookies to compile aggregate data about site traffic and site interaction so that we can offer better site experiences and tools in the future. We may contract with third-party service providers to assist us in better understanding our site visitors.</p>

        <h2>5. Contacting Us</h2>
        <p>If there are any questions regarding this privacy policy, you may contact us using the information on our <a href="contact.php" class="text-amber-600 font-bold hover:underline">Contact Page</a>.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
