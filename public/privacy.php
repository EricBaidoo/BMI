<?php
$pageTitle = 'Privacy Policy | Bridge Ministries International';
$pageDescription = 'How Bridge Ministries International collects, uses and protects your personal information in Ghana and the United States.';

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/settings.php';

$church = setting('site.name', 'Bridge Ministries International');
$privacyEmail = setting('contact.email_general', 'info@bmiglobal.org');
$address = setting('contact.address');
$retentionMonths = max(1, (int) env('MESSAGE_RETENTION_MONTHS', 24));
$updated = '4 October 2026';

include __DIR__ . '/../includes/header.php';

$h2 = 'text-2xl md:text-3xl font-display font-black uppercase text-black mt-14 mb-4';
$p = 'text-lg text-black/75 leading-relaxed mb-4';
$li = 'text-lg text-black/75 leading-relaxed';
?>

<!-- HERO SECTION -->
<div class="relative pt-32 pb-16 md:pt-48 md:pb-24 bg-[#000000] overflow-hidden">
    <div class="relative z-10 w-[90%] max-w-[112.5rem] mx-auto text-center">
        <h1 class="text-4xl md:text-7xl font-display font-black text-white tracking-normal mb-6">Privacy Policy</h1>
        <p class="text-xl text-white/50 max-w-2xl mx-auto font-medium">What we collect, why, how long we keep it, and your rights in Ghana and the United States.</p>
        <p class="text-sm text-white/40 mt-6">Last updated <?php echo e($updated); ?></p>
    </div>
</div>

<div class="py-20 md:py-24 bg-[#F5F5F5] text-black">
    <div class="max-w-3xl mx-auto px-6">

        <p class="<?php echo $p; ?>"><?php echo e($church); ?> (“we”, “us”) runs this website for our congregations in Ghana and the United States and for everyone who joins us online. This policy explains how we handle personal information you share with us through the website. We follow Ghana’s Data Protection Act, 2012 (Act 843), and the privacy laws that apply to our visitors in the United States.</p>

        <h2 class="<?php echo $h2; ?>">1. What we collect</h2>
        <p class="<?php echo $p; ?>">We only collect what you choose to send us:</p>
        <ul class="list-disc pl-6 space-y-2 mb-4">
            <li class="<?php echo $li; ?>"><strong>Contact and prayer requests:</strong> your name, email address, the subject you pick and your message.</li>
            <li class="<?php echo $li; ?>"><strong>Plan a Visit:</strong> your name, email, phone number (optional), the date you plan to visit and whether you are bringing children. We do not ask for children’s names or ages.</li>
            <li class="<?php echo $li; ?>"><strong>Newsletter:</strong> your email address.</li>
            <li class="<?php echo $li; ?>"><strong>Sermon notes by email:</strong> your email address, used once to send the notes you asked for.</li>
        </ul>
        <p class="<?php echo $p; ?>">Like most websites, our server also records technical information such as IP addresses, used only to keep the site secure (for example to stop spam and repeated failed sign-in attempts).</p>

        <h2 class="<?php echo $h2; ?>">2. How we use it</h2>
        <ul class="list-disc pl-6 space-y-2 mb-4">
            <li class="<?php echo $li; ?>">To reply to you, pray with you, and welcome you when you visit.</li>
            <li class="<?php echo $li; ?>">To send the newsletter you signed up for. Every newsletter includes an unsubscribe link, and you can also ask us to remove you at any time.</li>
            <li class="<?php echo $li; ?>">To keep the website and our staff accounts secure.</li>
        </ul>
        <p class="<?php echo $p; ?>">Prayer requests are seen only by the staff and ministers who handle them. <strong>We never sell or rent your personal information</strong>, and we do not share it for advertising.</p>

        <h2 class="<?php echo $h2; ?>">3. Giving</h2>
        <p class="<?php echo $p; ?>">Online gifts are processed by secure payment providers (for example Paystack in Ghana and Stripe in the United States). You enter your card, bank or Mobile Money details on their pages, not ours, and we never see or store full card numbers. Their privacy policies apply to the payment itself. We receive the gift amount, your name and email so we can keep accurate records and send receipts.</p>

        <h2 class="<?php echo $h2; ?>">4. Cookies and similar technology</h2>
        <ul class="list-disc pl-6 space-y-2 mb-4">
            <li class="<?php echo $li; ?>">When you use a form, we set one small security cookie that protects the form from misuse. It is deleted when you close your browser.</li>
            <li class="<?php echo $li; ?>">If we measure visits, we use privacy-friendly analytics that do not use cookies and do not track you across websites.</li>
            <li class="<?php echo $li; ?>">Sermon and livestream videos use YouTube’s privacy-enhanced mode, which does not set tracking cookies until you press play.</li>
            <li class="<?php echo $li; ?>">Your browser may remember small preferences on your own device, such as which country you give from.</li>
        </ul>
        <p class="<?php echo $p; ?>">We do not use advertising cookies.</p>

        <h2 class="<?php echo $h2; ?>">5. How long we keep it</h2>
        <ul class="list-disc pl-6 space-y-2 mb-4">
            <li class="<?php echo $li; ?>">Messages, prayer requests and visit requests are deleted automatically after <?php echo $retentionMonths; ?> months, or sooner if you ask.</li>
            <li class="<?php echo $li; ?>">Newsletter sign-ups are kept until you unsubscribe.</li>
            <li class="<?php echo $li; ?>">Security records of sign-in attempts are deleted after 30 days.</li>
        </ul>

        <h2 class="<?php echo $h2; ?>">6. Your rights</h2>
        <p class="<?php echo $p; ?>">Wherever you live, you can ask us to:</p>
        <ul class="list-disc pl-6 space-y-2 mb-4">
            <li class="<?php echo $li; ?>">tell you what personal information we hold about you and give you a copy;</li>
            <li class="<?php echo $li; ?>">correct anything that is wrong;</li>
            <li class="<?php echo $li; ?>">delete your information, or stop sending you emails.</li>
        </ul>
        <p class="<?php echo $p; ?>">Email <a href="mailto:<?php echo e($privacyEmail); ?>" class="text-amber-700 font-bold hover:underline"><?php echo e($privacyEmail); ?></a> with the subject “Privacy request”. We will reply within 30 days and will not treat you differently for making a request.</p>
        <p class="<?php echo $p; ?>"><strong>In Ghana</strong>, you may also complain to the Data Protection Commission. <strong>In the United States</strong>, residents of California and other states with privacy laws have these rights under those laws; we do not sell or share personal information as those laws define it.</p>

        <h2 class="<?php echo $h2; ?>">7. Children</h2>
        <p class="<?php echo $p; ?>">This website is meant for adults. We do not knowingly collect personal information from children under 13. If you believe a child has sent us information, contact us and we will delete it.</p>

        <h2 class="<?php echo $h2; ?>">8. How we protect it</h2>
        <p class="<?php echo $p; ?>">The site uses encrypted connections (HTTPS). Staff accounts require strong passwords and support two-step sign-in, access is limited by role, and every change is recorded. Our website and email may be hosted on servers outside Ghana, by providers that protect data to the standards of this policy.</p>

        <h2 class="<?php echo $h2; ?>">9. Changes and contact</h2>
        <p class="<?php echo $p; ?>">If we change this policy we will update the date at the top of this page. Questions are welcome:</p>
        <p class="<?php echo $p; ?>"><?php echo e($church); ?><br>
            <?php if ($address !== ''): ?><?php echo e($address); ?><br><?php endif; ?>
            <a href="mailto:<?php echo e($privacyEmail); ?>" class="text-amber-700 font-bold hover:underline"><?php echo e($privacyEmail); ?></a> · <a href="contact" class="text-amber-700 font-bold hover:underline">Contact page</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
