<?php
$pageTitle = 'Contact | Bridge Ministries International';
$pageDescription = 'Plan your visit, send us a message, or share a prayer request with Bridge Ministries International in Accra, Ghana.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/helpers.php';

$contactSuccess = false;
$contactError = '';
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => '', 'type' => 'contact'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    try {
        csrf_check();

        // Honeypot — bots fill it, humans don't see it.
        if (!empty($_POST['website_url'])) {
            $contactSuccess = true;
        } else {
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $subject = trim((string) ($_POST['subject'] ?? ''));
            $message = trim((string) ($_POST['message'] ?? ''));
            $type = (string) ($_POST['type'] ?? 'contact');
            $type = in_array($type, ['contact', 'prayer'], true) ? $type : 'contact';

            $old = compact('name', 'email', 'subject', 'message', 'type');

            if ($name === '' || $email === '' || $message === '') {
                throw new RuntimeException('Please fill in your name, email, and message.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Please enter a valid email address.');
            }
            if (mb_strlen($message) < 5) {
                throw new RuntimeException('Your message is too short.');
            }
            if (mb_strlen($message) > 5000) {
                throw new RuntimeException('Your message is too long (max 5000 characters).');
            }

            $pdo = db_connect();
            $stmt = $pdo->prepare(
                'INSERT INTO messages (full_name, email, subject, message, type) VALUES (:n, :e, :s, :m, :t)'
            );
            $stmt->execute([
                ':n' => $name,
                ':e' => $email,
                ':s' => $subject !== '' ? $subject : null,
                ':m' => $message,
                ':t' => $type,
            ]);

            $contactSuccess = true;
            $old = ['name' => '', 'email' => '', 'subject' => '', 'message' => '', 'type' => 'contact'];
        }
    } catch (Throwable $e) {
        $contactError = $e->getMessage();
    }
}

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Contact</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Plan Your Visit and Reach Out</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Questions, prayer requests, or first-time visit details? We are ready to help.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-6">
        <form method="post" action="contact.php#contact-form" id="contact-form" class="section-card icon-card space-y-3" data-icon="@" novalidate>
            <h2 class="text-xl font-semibold">Send a Message</h2>

            <?php if ($contactSuccess): ?>
                <div class="rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">
                    Thank you. Your message has been received and we will be in touch.
                </div>
            <?php endif; ?>
            <?php if ($contactError !== ''): ?>
                <div class="rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
                    <?php echo htmlspecialchars($contactError); ?>
                </div>
            <?php endif; ?>

            <?php echo csrf_field(); ?>
            <input type="hidden" name="contact_form" value="1">
            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                <label>Leave this field blank: <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
            </div>

            <div>
                <label class="text-sm font-medium" for="cf-name">Full Name</label>
                <input id="cf-name" type="text" name="name" required maxlength="120"
                       value="<?php echo htmlspecialchars($old['name']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="cf-email">Email</label>
                <input id="cf-email" type="email" name="email" required maxlength="150"
                       value="<?php echo htmlspecialchars($old['email']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="cf-subject">Subject (optional)</label>
                <input id="cf-subject" type="text" name="subject" maxlength="180"
                       value="<?php echo htmlspecialchars($old['subject']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="cf-type">This is a&hellip;</label>
                <select id="cf-type" name="type" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    <option value="contact" <?php echo $old['type'] === 'contact' ? 'selected' : ''; ?>>General message</option>
                    <option value="prayer" <?php echo $old['type'] === 'prayer' ? 'selected' : ''; ?>>Prayer request</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium" for="cf-message">Message</label>
                <textarea id="cf-message" name="message" rows="5" required maxlength="5000"
                          class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo htmlspecialchars($old['message']); ?></textarea>
            </div>
            <button type="submit" class="primary-action">Submit</button>
        </form>

        <div class="section-card icon-card" data-icon="i">
            <h2 class="font-semibold text-xl">Church Information</h2>
            <?php
                $address = setting('contact.address');
                $phone = setting('contact.phone_primary');
                $phone2 = setting('contact.phone_secondary');
                $emailGen = setting('contact.email_general');
                $emailPrayer = setting('contact.email_prayer');
                $emailGiving = setting('contact.email_giving');
                $svcSunday = setting('service.sunday_worship');
                $svcBible = setting('service.bible_study');
                $svcPrayer = setting('service.prayer_service');
                $mapQuery = setting('contact.map_query', $address);
            ?>
            <?php if ($address !== ''): ?>
                <p class="text-sm mt-3"><strong>Address:</strong> <?php echo nl2br(e($address)); ?></p>
            <?php endif; ?>
            <?php if ($phone !== ''): ?>
                <p class="text-sm"><strong>Phone:</strong>
                    <a href="tel:<?php echo e(preg_replace('/\s+/', '', $phone)); ?>" class="hover:underline"><?php echo e($phone); ?></a>
                    <?php if ($phone2 !== ''): ?>
                        &middot; <a href="tel:<?php echo e(preg_replace('/\s+/', '', $phone2)); ?>" class="hover:underline"><?php echo e($phone2); ?></a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if ($emailGen !== ''): ?>
                <p class="text-sm"><strong>General:</strong> <a href="mailto:<?php echo e($emailGen); ?>" class="hover:underline"><?php echo e($emailGen); ?></a></p>
            <?php endif; ?>
            <?php if ($emailPrayer !== ''): ?>
                <p class="text-sm"><strong>Prayer requests:</strong> <a href="mailto:<?php echo e($emailPrayer); ?>" class="hover:underline"><?php echo e($emailPrayer); ?></a></p>
            <?php endif; ?>
            <?php if ($emailGiving !== ''): ?>
                <p class="text-sm"><strong>Giving:</strong> <a href="mailto:<?php echo e($emailGiving); ?>" class="hover:underline"><?php echo e($emailGiving); ?></a></p>
            <?php endif; ?>

            <p class="text-sm mt-3 muted-copy">
                <?php
                    $svc = array_filter([$svcSunday, $svcBible, $svcPrayer]);
                    echo $svc ? 'Service Times: ' . e(implode(' · ', $svc)) : '';
                ?>
            </p>

            <?php if ($mapQuery !== ''): ?>
                <div class="mt-4 rounded-xl overflow-hidden">
                    <iframe
                        title="Church location map"
                        src="https://www.google.com/maps?q=<?php echo urlencode($mapQuery); ?>&output=embed"
                        width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-card mt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">First Time at BMI?</h2>
            <p class="text-sm muted-copy mt-1">Let us know you are coming and we will make your visit smooth and welcoming.</p>
        </div>
        <a href="index.php" class="secondary-action">Back to Home</a>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
