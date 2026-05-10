<?php
require_once __DIR__ . '/settings.php';

$siteName    = setting('site.name', 'Bridge Ministries International');
$address     = setting('contact.address');
$phone       = setting('contact.phone_primary');
$phone2      = setting('contact.phone_secondary');
$emailGen    = setting('contact.email_general');
$svcSunday   = setting('service.sunday_worship');
$svcBible    = setting('service.bible_study');
$svcPrayer   = setting('service.prayer_service');

$socials = array_filter([
    'facebook'  => setting('social.facebook'),
    'instagram' => setting('social.instagram'),
    'youtube'   => setting('social.youtube'),
    'x'         => setting('social.x'),
    'tiktok'    => setting('social.tiktok'),
]);

$socialIcons = [
    'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 22v-8h2.7l.4-3.1H13V8.9c0-.9.3-1.5 1.6-1.5H16V4.6c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8V11H7.4v3.1H10V22h3z"/></svg>',
    'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2-.1-1.2-.1-1.6-.1-4.8s0-3.6.1-4.8c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4 1.2-.1 1.6-.1 4.8-.1zM12 0C8.7 0 8.3 0 7.1.1 5.8.1 4.9.3 4.1.6c-.8.3-1.5.7-2.2 1.4S.7 3.4.4 4.2c-.3.8-.5 1.7-.5 3C-.1 8.3 0 8.7 0 12s0 3.7.1 4.9c.1 1.2.3 2.1.6 2.9.3.8.7 1.5 1.4 2.2.7.7 1.4 1.1 2.2 1.4.8.3 1.7.5 2.9.6 1.3.1 1.6.1 4.9.1s3.7 0 4.9-.1c1.2-.1 2.1-.3 2.9-.6.8-.3 1.5-.7 2.2-1.4.7-.7 1.1-1.4 1.4-2.2.3-.8.5-1.7.6-2.9.1-1.3.1-1.6.1-4.9s0-3.7-.1-4.9c-.1-1.2-.3-2.1-.6-2.9-.3-.8-.7-1.5-1.4-2.2-.7-.7-1.4-1.1-2.2-1.4-.8-.3-1.7-.5-2.9-.6C15.7 0 15.3 0 12 0zm0 5.8c-3.4 0-6.2 2.8-6.2 6.2s2.8 6.2 6.2 6.2 6.2-2.8 6.2-6.2-2.8-6.2-6.2-6.2zm0 10.2c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4zm6.4-11.8c-.8 0-1.4.6-1.4 1.4s.6 1.4 1.4 1.4 1.4-.6 1.4-1.4-.6-1.4-1.4-1.4z"/></svg>',
    'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2c-.3-1-1-1.8-2-2C19.7 3.8 12 3.8 12 3.8s-7.7 0-9.5.4c-1 .3-1.8 1-2 2C0 8 0 12 0 12s0 4 .5 5.8c.3 1 1 1.8 2 2C4.3 20.2 12 20.2 12 20.2s7.7 0 9.5-.4c1-.3 1.8-1 2-2 .5-1.8.5-5.8.5-5.8s0-4-.5-5.8zM9.6 15.6V8.4l6.4 3.6-6.4 3.6z"/></svg>',
    'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    'tiktok'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5.8 20.1a6.34 6.34 0 0 0 10.86-4.43V8.83a8.16 8.16 0 0 0 4.77 1.52V6.91a4.85 4.85 0 0 1-1.84-.22z"/></svg>',
];
?>
</main>
<footer class="site-footer">
    <div class="max-w-6xl mx-auto px-4 py-10 grid md:grid-cols-4 gap-6">
        <div class="md:col-span-2">
            <p class="font-semibold text-lg"><?php echo htmlspecialchars($siteName); ?></p>
            <p class="text-sm text-slate-300 mt-2 max-w-md">Helping people know Christ, grow in faith, and live on mission. You are always welcome at BMI.</p>

            <div class="text-sm text-slate-300 mt-4 space-y-1">
                <?php if ($address !== ''): ?>
                    <p><?php echo nl2br(htmlspecialchars($address)); ?></p>
                <?php endif; ?>
                <?php if ($phone !== ''): ?>
                    <p><a class="hover:underline" href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $phone)); ?>"><?php echo htmlspecialchars($phone); ?></a>
                    <?php if ($phone2 !== ''): ?>
                        &middot; <a class="hover:underline" href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $phone2)); ?>"><?php echo htmlspecialchars($phone2); ?></a>
                    <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ($emailGen !== ''): ?>
                    <p><a class="hover:underline" href="mailto:<?php echo htmlspecialchars($emailGen); ?>"><?php echo htmlspecialchars($emailGen); ?></a></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($socials)): ?>
                <div class="social-icons">
                    <?php foreach ($socials as $key => $url): ?>
                        <?php if (isset($socialIcons[$key])): ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener noreferrer"
                               aria-label="<?php echo htmlspecialchars(ucfirst($key)); ?>">
                                <?php echo $socialIcons[$key]; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <p class="font-semibold">Quick Links</p>
            <div class="mt-2 flex flex-col gap-1 text-sm">
                <a href="about.php" class="footer-link">About</a>
                <a href="beliefs.php" class="footer-link">Our Beliefs</a>
                <a href="visit.php" class="footer-link">Plan Your Visit</a>
                <a href="sermons.php" class="footer-link">Sermons</a>
                <a href="events.php" class="footer-link">Events</a>
                <a href="ministries.php" class="footer-link">Ministries</a>
                <a href="blog.php" class="footer-link">Good News</a>
                <a href="donate.php" class="footer-link">Give</a>
                <a href="contact.php" class="footer-link">Contact</a>
            </div>
        </div>

        <div>
            <p class="font-semibold">Service Times</p>
            <div class="mt-2 text-sm text-slate-300 space-y-1">
                <?php if ($svcSunday !== ''): ?><p><?php echo htmlspecialchars($svcSunday); ?></p><?php endif; ?>
                <?php if ($svcBible !== ''): ?><p><?php echo htmlspecialchars($svcBible); ?></p><?php endif; ?>
                <?php if ($svcPrayer !== ''): ?><p><?php echo htmlspecialchars($svcPrayer); ?></p><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-4 border-t border-white/10 text-xs text-slate-400 flex flex-wrap justify-between gap-2">
        <span>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</span>
        <span><a href="admin/login.php" class="hover:underline">Staff Sign-in</a></span>
    </div>
</footer>
<script src="assets/js/main.js?v=<?php echo urlencode((string) filemtime(__DIR__ . '/../assets/js/main.js')); ?>"></script>
</body>
</html>
