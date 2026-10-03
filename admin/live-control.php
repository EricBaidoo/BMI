<?php
require_once __DIR__ . '/../includes/auth.php';
auth_require();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_check();
        $pdo = db_connect();
        $stmt = $pdo->prepare("INSERT INTO live_state (setting_key, setting_value) VALUES (:key, :val) ON DUPLICATE KEY UPDATE setting_value = :val2");
        foreach (['current_prompt_html', 'current_notes_html'] as $key) {
            $value = (string) ($_POST[$key] ?? '');
            $stmt->execute([':key' => $key, ':val' => $value, ':val2' => $value]);
        }
        header('Location: live-control.php?status=updated');
        exit;
    } catch (Throwable $e) {
        error_log((string) $e);
        $error = 'Unable to save the live state. Please try again.';
    }
}

$feedback = ($_GET['status'] ?? '') === 'updated' ? 'Live state updated. Viewers will see it within a few seconds.' : '';

$currentPrompt = '';
$currentNotes = '';
try {
    $settings = db_connect()->query("SELECT setting_key, setting_value FROM live_state")->fetchAll(PDO::FETCH_KEY_PAIR);
    $currentPrompt = $settings['current_prompt_html'] ?? '';
    $currentNotes = $settings['current_notes_html'] ?? '';
} catch (Throwable $e) {
    error_log((string) $e);
    $error = $error ?: 'Unable to load the current live state.';
}

$pageTitle = 'Live Stream Control';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($feedback !== ''): ?>
    <div class="mb-6 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($feedback); ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="mb-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="sm:flex sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Live Stream Control</h1>
        <p class="mt-2 text-sm text-gray-700">Push real-time interactive prompts and fill-in-the-blank notes to viewers on the livestream page.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="../livestream.php" target="_blank" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
            View Livestream Page
        </a>
    </div>
</div>

<div class="bg-white shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg overflow-hidden">
    <div class="p-6">
        <form method="POST" action="live-control.php" class="space-y-8">
            <?php echo csrf_field(); ?>
            
            <!-- Real-Time Prompt -->
            <div>
                <label for="current_prompt_html" class="block text-sm font-medium text-gray-700">Real-Time Prompt (HTML)</label>
                <p class="text-xs text-gray-500 mb-2">Leave blank to hide the prompt. Example: <code>&lt;a href="donate.php" class="..."&gt;Give Now&lt;/a&gt;</code></p>
                <div class="mt-1">
                    <textarea id="current_prompt_html" name="current_prompt_html" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"><?php echo htmlspecialchars($currentPrompt); ?></textarea>
                </div>
                <!-- Presets for Prompt -->
                <div class="mt-3 flex gap-2">
                    <button type="button" onclick="setPromptPreset('give')" class="inline-flex items-center rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Preset: Give Now</button>
                    <button type="button" onclick="setPromptPreset('salvation')" class="inline-flex items-center rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Preset: Accept Christ</button>
                    <button type="button" onclick="setPromptPreset('clear')" class="inline-flex items-center rounded border border-red-300 bg-white px-2.5 py-1.5 text-xs font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Clear Prompt</button>
                </div>
            </div>

            <!-- Sermon Notes -->
            <div>
                <label for="current_notes_html" class="block text-sm font-medium text-gray-700">Interactive Sermon Notes (HTML)</label>
                <p class="text-xs text-gray-500 mb-2">Use inputs for fill-in-the-blanks. Example: <code>God is &lt;input type="text" class="notes-input" placeholder="..."&gt;</code></p>
                <div class="mt-1">
                    <textarea id="current_notes_html" name="current_notes_html" rows="15" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"><?php echo htmlspecialchars($currentNotes); ?></textarea>
                </div>
                <!-- Presets for Notes -->
                <div class="mt-3 flex gap-2">
                    <button type="button" onclick="setNotesPreset('sample')" class="inline-flex items-center rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Load Sample Outline</button>
                    <button type="button" onclick="setNotesPreset('clear')" class="inline-flex items-center rounded border border-red-300 bg-white px-2.5 py-1.5 text-xs font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Clear Notes</button>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Push Live Updates
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setPromptPreset(type) {
    const textarea = document.getElementById('current_prompt_html');
    if (type === 'give') {
        textarea.value = '<div class="flex items-center gap-6"><div class="text-white"><h4 class="font-bold text-lg mb-1">Tithes & Offerings</h4><p class="text-white/70 text-sm">Join us in generosity as we worship through giving.</p></div><a href="donate.php" class="bg-amber-500 text-black px-6 py-3 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-white transition-colors whitespace-nowrap">Give Now</a></div>';
    } else if (type === 'salvation') {
        textarea.value = '<div class="flex items-center gap-6"><div class="text-white"><h4 class="font-bold text-lg mb-1">Make a Decision Today</h4><p class="text-white/70 text-sm">If you just gave your life to Christ, let us know!</p></div><a href="contact.php?subject=Salvation" class="bg-red-600 text-white px-6 py-3 rounded-full font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-black transition-colors whitespace-nowrap">I Accepted Christ</a></div>';
    } else if (type === 'clear') {
        textarea.value = '';
    }
}

function setNotesPreset(type) {
    const textarea = document.getElementById('current_notes_html');
    if (type === 'sample') {
        textarea.value = `<h3 class="text-2xl font-bold text-white mb-4">The Power of Faith</h3>
<p class="text-white/60 mb-6 font-medium text-sm">Pastor John Doe &mdash; September 20, 2026</p>

<div class="space-y-6 text-white/80 leading-loose">
    <p>1. Faith is not just a feeling, it is an <input type="text" class="notes-input" placeholder="action">.</p>
    <p>2. We walk by faith, not by <input type="text" class="notes-input" placeholder="sight">.</p>
    
    <div class="bg-white/5 p-4 rounded-xl border-l-4 border-amber-500 my-6">
        <p class="italic text-white/90">"Now faith is confidence in what we hope for and assurance about what we do not see." - Hebrews 11:1</p>
    </div>
    
    <p>3. God's promises are <input type="text" class="notes-input" placeholder="yes"> and <input type="text" class="notes-input" placeholder="amen">.</p>
    
    <div class="mt-8 pt-6 border-t border-white/10">
        <p class="text-sm text-white/50 mb-2">Personal Reflection:</p>
        <textarea class="notes-textarea" rows="3" placeholder="What is God speaking to you today?"></textarea>
    </div>
</div>`;
    } else if (type === 'clear') {
        textarea.value = '';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
