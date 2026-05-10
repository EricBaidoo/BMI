<?php
$pageTitle = 'Give | Bridge Ministries International';
$pageDescription = 'Support the mission of Bridge Ministries International with a secure online gift.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/settings.php';

// Settings override env defaults
$paystackPublicKey = setting('giving.paystack_public_key', $paystackPublicKey);
$givingCurrency    = setting('giving.currency', 'GHS');
$bankName          = setting('giving.bank_name');
$bankAccountName   = setting('giving.bank_account_name');
$bankAccountNum    = setting('giving.bank_account_number');
$bankBranch        = setting('giving.bank_branch');
$momoMtn           = setting('giving.momo_mtn');
$momoVoda          = setting('giving.momo_vodafone');
$momoAt            = setting('giving.momo_airteltigo');
$emailGiving       = setting('contact.email_giving');

$paystackEnabled = $paystackPublicKey !== '';
include 'includes/header.php';
?>
<section class="page-hero">
    <div class="max-w-6xl mx-auto px-4 py-14 md:py-16">
        <span class="tag-chip">Give</span>
        <h1 class="text-4xl md:text-5xl font-bold mt-3">Support the Mission</h1>
        <p class="mt-4 text-lg muted-copy max-w-3xl">Your giving helps us preach the Gospel, disciple believers, and serve people in practical ways.</p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-6">
        <div class="section-card">
            <h2 class="font-semibold text-2xl">Give Online</h2>
            <?php if ($paystackEnabled): ?>
                <p class="text-sm mt-2 muted-copy">Secure card and mobile-money giving via Paystack. You will receive an email receipt.</p>

                <form id="give-form" class="mt-4 space-y-3" onsubmit="return false;">
                    <div>
                        <label class="text-sm font-medium" for="give-name">Full Name</label>
                        <input id="give-name" type="text" required maxlength="120"
                               class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm font-medium" for="give-email">Email</label>
                        <input id="give-email" type="email" required maxlength="150"
                               class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm font-medium" for="give-amount">Amount (<?php echo e($givingCurrency); ?>)</label>
                        <input id="give-amount" type="number" min="1" step="1" required
                               class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm font-medium" for="give-purpose">Purpose</label>
                        <select id="give-purpose" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                            <option value="tithe">Tithe</option>
                            <option value="offering">Offering</option>
                            <option value="missions">Missions</option>
                            <option value="building">Building Fund</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <button id="give-submit" type="submit" class="primary-action">Give Securely</button>
                    <p id="give-status" class="text-sm mt-2" role="status" aria-live="polite"></p>
                </form>
            <?php else: ?>
                <p class="text-sm mt-2 muted-copy">Online giving is being set up. In the meantime, please use one of the channels listed on the right, or contact our finance team.</p>
            <?php endif; ?>
        </div>

        <div class="section-card">
            <h2 class="font-semibold text-2xl">Other Ways to Give</h2>

            <ul class="list-disc pl-5 mt-3 text-sm space-y-2">
                <li>In person during any church service.</li>
                <?php if ($bankName !== '' || $bankAccountNum !== ''): ?>
                    <li>
                        <strong>Bank transfer:</strong>
                        <?php if ($bankName !== ''): ?><?php echo e($bankName); ?><?php endif; ?>
                        <?php if ($bankAccountName !== ''): ?> &middot; <?php echo e($bankAccountName); ?><?php endif; ?>
                        <?php if ($bankAccountNum !== ''): ?> &middot; A/C <?php echo e($bankAccountNum); ?><?php endif; ?>
                        <?php if ($bankBranch !== ''): ?> &middot; <?php echo e($bankBranch); ?> Branch<?php endif; ?>
                    </li>
                <?php else: ?>
                    <li>Bank transfer (account details available from the church office).</li>
                <?php endif; ?>

                <?php if ($momoMtn !== '' || $momoVoda !== '' || $momoAt !== ''): ?>
                    <li>
                        <strong>Mobile money:</strong>
                        <?php $mm = []; if ($momoMtn !== '') $mm[] = 'MTN MoMo ' . $momoMtn; if ($momoVoda !== '') $mm[] = 'Vodafone Cash ' . $momoVoda; if ($momoAt !== '') $mm[] = 'AirtelTigo ' . $momoAt; ?>
                        <?php echo e(implode(' · ', $mm)); ?>
                    </li>
                <?php else: ?>
                    <li>Mobile money (MTN MoMo, Vodafone Cash, AirtelTigo).</li>
                <?php endif; ?>
            </ul>

            <h3 class="font-semibold text-lg mt-6">Need Assistance?</h3>
            <p class="text-sm mt-2 muted-copy">For giving support or designated offerings, contact our finance team<?php if ($emailGiving !== ''): ?> at <a class="hover:underline" href="mailto:<?php echo e($emailGiving); ?>"><?php echo e($emailGiving); ?></a><?php endif; ?>.</p>
            <a href="contact.php" class="secondary-action mt-4">Contact Finance Team</a>
        </div>
    </div>

    <div class="section-card mt-6">
        <h2 class="font-semibold text-xl">Thank You for Giving</h2>
        <p class="text-sm mt-2">Every contribution makes a meaningful difference in worship services, outreach projects, and ministry development.</p>
    </div>
</section>

<?php if ($paystackEnabled): ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
(function () {
    var form = document.getElementById('give-form');
    if (!form) return;

    var btn = document.getElementById('give-submit');
    var status = document.getElementById('give-status');

    btn.addEventListener('click', function () {
        var name = document.getElementById('give-name').value.trim();
        var email = document.getElementById('give-email').value.trim();
        var amount = parseInt(document.getElementById('give-amount').value, 10);
        var purpose = document.getElementById('give-purpose').value;

        if (!name || !email || !amount || amount < 1) {
            status.textContent = 'Please fill in all fields with a valid amount.';
            status.className = 'text-sm mt-2 text-red-700';
            return;
        }

        status.textContent = 'Opening secure payment...';
        status.className = 'text-sm mt-2 text-slate-600';

        var handler = PaystackPop.setup({
            key: <?php echo json_encode($paystackPublicKey); ?>,
            email: email,
            amount: amount * 100, // smallest currency unit
            currency: <?php echo json_encode($givingCurrency); ?>,
            ref: 'bmi-' + Date.now() + '-' + Math.floor(Math.random() * 1e6),
            metadata: {
                custom_fields: [
                    { display_name: 'Donor Name', variable_name: 'donor_name', value: name },
                    { display_name: 'Purpose', variable_name: 'purpose', value: purpose }
                ]
            },
            callback: function (response) {
                status.textContent = 'Thank you! Reference: ' + response.reference;
                status.className = 'text-sm mt-2 text-emerald-700';
            },
            onClose: function () {
                status.textContent = 'Payment window closed. You may try again any time.';
                status.className = 'text-sm mt-2 text-slate-600';
            }
        });
        handler.openIframe();
    });
})();
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
