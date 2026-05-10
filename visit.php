<?php
$pageTitle = 'Plan Your Visit | Bridge Ministries International';
$pageDescription = 'Planning your first visit to Bridge Ministries International? Here\'s everything you need to know about service times, parking, kids, and what to expect.';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/helpers.php';

// Reuse the contact form mechanics for the "Connect Card" submission
$visitError = '';
$old = ['name' => '', 'email' => '', 'message' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connect_card'])) {
    try {
        csrf_check();

        // Honeypot — bots fill it, humans don't see it. Silent success.
        if (!empty($_POST['website_url'])) {
            flash('visit', 'success');
            header('Location: visit#connect-form');
            exit;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? 'Plans to visit BMI.'));

        $old = compact('name', 'email', 'phone', 'message');

        if ($name === '' || $email === '') {
            throw new RuntimeException('Please fill in your name and email.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }

        $compiled = "Connect card submission.\n\nName: $name\nEmail: $email\nPhone: $phone\n\nNotes:\n" . $message;

        $pdo = db_connect();
        $stmt = $pdo->prepare(
            'INSERT INTO messages (full_name, email, subject, message, type) VALUES (:n, :e, :s, :m, "contact")'
        );
        $stmt->execute([
            ':n' => $name,
            ':e' => $email,
            ':s' => 'Connect Card · Plan Your Visit',
            ':m' => $compiled,
        ]);

        flash('visit', 'success');
        header('Location: visit#connect-form');
        exit;
    } catch (Throwable $e) {
        $visitError = $e->getMessage();
    }
}

$visitSuccess = flash('visit') === 'success';

$svcSunday = setting('service.sunday_worship');
$svcBible = setting('service.bible_study');
$svcPrayer = setting('service.prayer_service');
$address = setting('contact.address');
$phone = setting('contact.phone_primary');
$mapQuery = setting('contact.map_query', $address);

include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Plan Your Visit</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">We've saved you a seat.</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">
            Thinking about visiting? You're already welcome. Here's everything you need to know about your first time at BMI —
            from where to park to what to wear, and what your kids can expect.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12 space-y-10">

    <!-- At-a-glance row -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="section-card icon-card" data-icon="•">
            <p class="text-xs uppercase tracking-wide muted-copy">When</p>
            <p class="font-semibold mt-1"><?php echo e($svcSunday !== '' ? $svcSunday : 'Sundays'); ?></p>
            <?php if ($svcBible !== '' || $svcPrayer !== ''): ?>
                <p class="text-xs muted-copy mt-2">
                    <?php $other = array_filter([$svcBible, $svcPrayer]); echo e(implode(' · ', $other)); ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="section-card icon-card" data-icon="•">
            <p class="text-xs uppercase tracking-wide muted-copy">Where</p>
            <p class="font-semibold mt-1"><?php echo e($address !== '' ? $address : 'Accra, Ghana'); ?></p>
        </div>
        <div class="section-card icon-card" data-icon="•">
            <p class="text-xs uppercase tracking-wide muted-copy">Need help finding us?</p>
            <p class="font-semibold mt-1"><?php echo e($phone !== '' ? $phone : 'Call the church office'); ?></p>
            <p class="text-xs muted-copy mt-2">Call or WhatsApp on the day — someone will guide you in.</p>
        </div>
    </div>

    <!-- What to expect -->
    <div>
        <div class="text-center max-w-2xl mx-auto">
            <span class="tag-chip">What to Expect</span>
            <h2 class="text-3xl font-bold mt-3">Your First Sunday at BMI</h2>
            <p class="mt-3 muted-copy">No pressure. No surprises. Just a warm family ready to welcome you.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div class="section-card">
                <p class="text-sm font-semibold text-blue-700">Step 1</p>
                <h3 class="text-lg font-semibold mt-1">Arrive a few minutes early</h3>
                <p class="mt-3 text-sm muted-copy">Aim to arrive about 15 minutes before service. Look for our hosts at the entrance — they're easy to spot and ready to help you find a seat, the kids' area, or the restrooms.</p>
            </div>
            <div class="section-card">
                <p class="text-sm font-semibold text-blue-700">Step 2</p>
                <h3 class="text-lg font-semibold mt-1">Worship with us</h3>
                <p class="mt-3 text-sm muted-copy">Our service runs about 90&nbsp;minutes and includes singing, prayer, scripture reading, and a Bible-centred message you can take with you into the week.</p>
            </div>
            <div class="section-card">
                <p class="text-sm font-semibold text-blue-700">Step 3</p>
                <h3 class="text-lg font-semibold mt-1">Stay and connect</h3>
                <p class="mt-3 text-sm muted-copy">After the service, stop by the welcome desk. Fill out a quick connect card and one of our pastors will follow up to answer any question, big or small.</p>
            </div>
        </div>
    </div>

    <!-- FAQ grid -->
    <div>
        <div class="text-center max-w-2xl mx-auto">
            <span class="tag-chip">FAQ</span>
            <h2 class="text-3xl font-bold mt-3">Common Questions</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-8">
            <div class="section-card">
                <h3 class="font-semibold">What should I wear?</h3>
                <p class="mt-2 text-sm muted-copy">Whatever you're comfortable in. Some come in suits, others in jeans — both are welcome. Come as you are.</p>
            </div>
            <div class="section-card">
                <h3 class="font-semibold">Is there parking?</h3>
                <p class="mt-2 text-sm muted-copy">Yes — free parking is available on-site. Look out for our parking attendants on Sundays.</p>
            </div>
            <div class="section-card">
                <h3 class="font-semibold">What about my kids?</h3>
                <p class="mt-2 text-sm muted-copy">We have a vibrant, age-appropriate Children's Ministry running during the Sunday service. It's safe, fun, and Bible-centred — your kids will love it.</p>
            </div>
            <div class="section-card">
                <h3 class="font-semibold">Will I be asked to give?</h3>
                <p class="mt-2 text-sm muted-copy">Giving is part of our worship, but we never want guests to feel pressured. As a first-time visitor, our gift to you is the seat — no expectation to give.</p>
            </div>
            <div class="section-card">
                <h3 class="font-semibold">Do you have a livestream?</h3>
                <p class="mt-2 text-sm muted-copy">Yes — if you can't make it in person, you can join us live online. <a href="livestream" class="text-teal-700 hover:underline">Watch live &rarr;</a></p>
            </div>
            <div class="section-card">
                <h3 class="font-semibold">I'm not sure I believe yet — can I still come?</h3>
                <p class="mt-2 text-sm muted-copy">Absolutely. You don't have to have it all figured out. Whether you're exploring, doubting, or just curious — there's a seat with your name on it.</p>
            </div>
        </div>
    </div>

    <!-- Connect card -->
    <div class="grid md:grid-cols-2 gap-6">
        <form method="post" id="connect-form" class="section-card icon-card space-y-3" data-icon="@" novalidate>
            <h2 class="text-xl font-semibold">Send a Connect Card</h2>
            <p class="text-sm muted-copy">Let us know you're coming or have a question — a pastor will reach out personally.</p>

            <?php if ($visitSuccess): ?>
                <div class="rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-3 py-2 text-sm">
                    Thank you. We've received your card and one of our pastors will be in touch soon.
                </div>
            <?php endif; ?>
            <?php if ($visitError !== ''): ?>
                <div class="rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2 text-sm">
                    <?php echo e($visitError); ?>
                </div>
            <?php endif; ?>

            <?php echo csrf_field(); ?>
            <input type="hidden" name="connect_card" value="1">
            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                <label>Leave this field blank: <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
            </div>

            <div>
                <label class="text-sm font-medium" for="vc-name">Full Name</label>
                <input id="vc-name" type="text" name="name" required maxlength="120"
                       value="<?php echo e($old['name']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="vc-email">Email</label>
                <input id="vc-email" type="email" name="email" required maxlength="150"
                       value="<?php echo e($old['email']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="vc-phone">Phone (optional)</label>
                <input id="vc-phone" type="tel" name="phone" maxlength="40"
                       value="<?php echo e($old['phone']); ?>"
                       class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="text-sm font-medium" for="vc-message">Notes / Questions</label>
                <textarea id="vc-message" name="message" rows="3" maxlength="2000"
                          placeholder="e.g. I'd like to bring my family this Sunday."
                          class="mt-1 w-full border border-slate-300 rounded px-3 py-2"><?php echo e($old['message']); ?></textarea>
            </div>
            <button type="submit" class="primary-action">Send Card</button>
        </form>

        <div class="section-card">
            <h2 class="text-xl font-semibold">Find Us on the Map</h2>
            <?php if ($mapQuery !== ''): ?>
                <div class="mt-3 rounded-xl overflow-hidden">
                    <iframe
                        title="Bridge Ministries International location"
                        src="https://www.google.com/maps?q=<?php echo urlencode($mapQuery); ?>&output=embed"
                        width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            <?php endif; ?>
            <p class="text-sm muted-copy mt-3">Lost? Call <?php echo e($phone !== '' ? $phone : 'the church office'); ?> on Sunday morning and we'll guide you in.</p>
        </div>
    </div>

</section>
<?php include 'includes/footer.php'; ?>
