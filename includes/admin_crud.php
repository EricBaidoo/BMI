<?php
/**
 * Shared admin editor for simple content types (sermons, events, blog posts).
 *
 * A page describes its content type once (table, fields, list card) and calls
 * admin_crud_page($resource). This file does the rest: validation, image upload or link,
 * slugs, publish toggles, audit log entries, the add/edit form, the list and pagination.
 *
 * Field types: text, textarea, date, time, url, select, publish (checkbox stored as a
 * publish timestamp or NULL). Field options:
 *   label, type, required, max (length), options (select), default, hint, rows, wide (full row)
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/uploads.php';

/**
 * Validate the posted fields. Returns [column => value] ready to save (empty optional fields become NULL).
 */
function admin_crud_values(array $resource, array $post, ?array $existing): array
{
    $values = [];
    foreach ($resource['fields'] as $name => $f) {
        $type = $f['type'] ?? 'text';
        $label = $f['label'] ?? $name;

        if ($type === 'publish') {
            $on = ($post[$name] ?? '') === '1';
            $previous = $existing[$name] ?? null;
            $values[$name] = $on ? ($previous ?: date('Y-m-d H:i:s')) : null;
            continue;
        }

        $value = trim((string) ($post[$name] ?? ''));
        if ($value === '') {
            if (!empty($f['required'])) {
                throw new RuntimeException("{$label} is required.");
            }
            $values[$name] = array_key_exists('default', $f) ? $f['default'] : null;
            continue;
        }
        if (!empty($f['max']) && mb_strlen($value) > (int) $f['max']) {
            throw new RuntimeException("{$label} must be at most {$f['max']} characters.");
        }
        switch ($type) {
            case 'select':
                if (!array_key_exists($value, $f['options'])) {
                    throw new RuntimeException("Choose a valid {$label}.");
                }
                break;
            case 'date':
                $d = DateTime::createFromFormat('Y-m-d', $value);
                if (!$d || $d->format('Y-m-d') !== $value) {
                    throw new RuntimeException("{$label} must be a valid date.");
                }
                break;
            case 'time':
                if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $value)) {
                    throw new RuntimeException("{$label} must be a valid time.");
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $value)) {
                    throw new RuntimeException("{$label} must be a full link starting with https://");
                }
                break;
        }
        $values[$name] = $value;
    }
    return $values;
}

/** Unique slug for $title in $table.$column (ignores the row being edited). */
function admin_crud_unique_slug(PDO $pdo, string $table, string $column, string $title, int $ignoreId = 0): string
{
    $base = slugify($title);
    $slug = $base;
    $check = $pdo->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :s AND id != :id");
    for ($i = 2; ; $i++) {
        $check->execute([':s' => $slug, ':id' => $ignoreId]);
        if ((int) $check->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i;
    }
}

/** Handles POST (add, edit, delete). Redirects on success; returns an error message on failure. */
function admin_crud_handle_post(array $r): string
{
    try {
        csrf_check();
        $pdo = db_connect();
        $table = $r['table'];
        $action = (string) ($_POST['action'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);
        $imageField = $r['image']['field'] ?? null;

        $existing = null;
        if ($action === 'edit' || $action === 'delete') {
            $stmt = $pdo->prepare("SELECT * FROM `{$table}` WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch() ?: null;
            if (!$existing) {
                throw new RuntimeException($r['singular'] . ' not found. It may have been deleted already.');
            }
        }

        if ($action === 'delete') {
            $pdo->prepare("DELETE FROM `{$table}` WHERE id = :id")->execute([':id' => $id]);
            if ($imageField) {
                upload_delete($existing[$imageField] ?? null);
            }
            audit('delete', $r['entity'], $id, 'Deleted ' . strtolower($r['singular']) . ': ' . ($existing[$r['title_field']] ?? '#' . $id));
            header('Location: ' . $r['page'] . '?status=deleted');
            exit;
        }

        if ($action !== 'add' && $action !== 'edit') {
            throw new RuntimeException('Unknown action.');
        }

        $values = admin_crud_values($r, $_POST, $existing);

        if ($imageField) {
            $uploaded = handle_image_upload_or_link($_FILES[$imageField] ?? null, (string) ($_POST[$imageField . '_url'] ?? ''), '', $r['image']['category'] ?? 'uncategorized');
            if ($uploaded !== null) {
                $values[$imageField] = $uploaded;
                if ($existing && ($existing[$imageField] ?? '') !== $uploaded) {
                    upload_delete($existing[$imageField]);
                }
            } elseif (!empty($_POST[$imageField . '_remove'])) {
                $values[$imageField] = null;
                if ($existing) {
                    upload_delete($existing[$imageField]);
                }
            } else {
                $values[$imageField] = $existing[$imageField] ?? null;
            }
        }

        // Slugs are created once and then kept, so shared links keep working after a title edit.
        if (!empty($r['slug'])) {
            $slugCol = $r['slug']['field'];
            $values[$slugCol] = ($existing[$slugCol] ?? '') !== ''
                ? $existing[$slugCol]
                : admin_crud_unique_slug($pdo, $table, $slugCol, (string) $values[$r['slug']['from']], $id);
        }

        $columns = array_keys($values);
        $title = (string) ($values[$r['title_field']] ?? '');
        if ($action === 'add') {
            $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table,
                implode(', ', array_map(fn ($c) => "`{$c}`", $columns)),
                implode(', ', array_map(fn ($c) => ":{$c}", $columns)));
            $pdo->prepare($sql)->execute(array_combine(array_map(fn ($c) => ":{$c}", $columns), array_values($values)));
            audit('create', $r['entity'], $pdo->lastInsertId(), 'Added ' . strtolower($r['singular']) . ': ' . $title);
            header('Location: ' . $r['page'] . '?status=added');
            exit;
        }

        $sql = sprintf('UPDATE `%s` SET %s WHERE id = :id', $table, implode(', ', array_map(fn ($c) => "`{$c}` = :{$c}", $columns)));
        $params = array_combine(array_map(fn ($c) => ":{$c}", $columns), array_values($values));
        $params[':id'] = $id;
        $pdo->prepare($sql)->execute($params);

        $changes = [];
        foreach ($values as $col => $new) {
            if ((string) ($existing[$col] ?? '') !== (string) ($new ?? '')) {
                $changes[$col] = [mb_strimwidth((string) ($existing[$col] ?? ''), 0, 300, '…'), mb_strimwidth((string) ($new ?? ''), 0, 300, '…')];
            }
        }
        audit('update', $r['entity'], $id, 'Updated ' . strtolower($r['singular']) . ': ' . $title, $changes ? ['changes' => $changes] : []);
        header('Location: ' . $r['page'] . '?status=updated');
        exit;
    } catch (Throwable $e) {
        return user_error_message($e);
    }
}

/** Full admin page for a resource. */
function admin_crud_page(array $r): void
{
    $error = '';
    $repost = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = admin_crud_handle_post($r);
        $repost = $_POST; // keep what was typed when validation fails
    }

    $editing = null;
    $pdo = null;
    try {
        $pdo = db_connect();
        $editId = (int) ($_GET['edit'] ?? 0);
        if (($repost['action'] ?? '') === 'edit') {
            $editId = (int) ($repost['id'] ?? 0);
        }
        if ($editId > 0) {
            $stmt = $pdo->prepare("SELECT * FROM `{$r['table']}` WHERE id = :id");
            $stmt->execute([':id' => $editId]);
            $editing = $stmt->fetch() ?: null;
            if (!$editing && $error === '') {
                $error = $r['singular'] . ' not found.';
            }
        }
    } catch (Throwable $e) {
        $error = $error ?: user_error_message($e, 'Unable to load ' . strtolower($r['singular']) . '.');
    }

    $form = $editing ?? [];
    if ($repost && ($repost['action'] ?? '') !== 'delete') {
        foreach ($r['fields'] as $name => $f) {
            $form[$name] = ($f['type'] ?? '') === 'publish' ? (($repost[$name] ?? '') === '1' ? '1' : null) : (string) ($repost[$name] ?? '');
        }
    }

    $feedback = [
        'added' => $r['singular'] . ' added.',
        'updated' => $r['singular'] . ' updated.',
        'deleted' => $r['singular'] . ' deleted.',
    ][(string) ($_GET['status'] ?? '')] ?? '';

    $rows = [];
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $perPage = (int) ($r['per_page'] ?? 15);
    $totalPages = 1;
    if ($pdo) {
        try {
            $total = (int) $pdo->query("SELECT COUNT(*) FROM `{$r['table']}`")->fetchColumn();
            $totalPages = max(1, (int) ceil($total / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            $rows = $pdo->query("SELECT * FROM `{$r['table']}` ORDER BY {$r['order']} LIMIT {$perPage} OFFSET {$offset}")->fetchAll();
        } catch (Throwable $e) {
            $error = $error ?: user_error_message($e, 'Unable to load ' . strtolower($r['plural']) . '.');
        }
    }

    $pageTitle = 'Manage ' . $r['plural'] . ' | BMI Admin';
    require ADMIN_TEMPLATES . '/header.php';
    admin_crud_render($r, compact('error', 'feedback', 'editing', 'form', 'rows', 'page', 'totalPages'));
    require ADMIN_TEMPLATES . '/footer.php';
}

function admin_crud_image_src(?string $path): string
{
    $path = safe_url((string) $path);
    if ($path === '') {
        return '';
    }
    return preg_match('#^https?://#i', $path) ? $path : '../' . ltrim($path, '/');
}

function admin_crud_render(array $r, array $s): void
{
    $input = 'w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all placeholder:text-slate-400';
    $editing = $s['editing'];
    $form = $s['form'];
    $imageField = $r['image']['field'] ?? null;
    ?>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Manage <?php echo e($r['plural']); ?></h1>
            <p class="mt-1 text-slate-500"><?php echo e($r['intro'] ?? ''); ?></p>
        </div>

        <?php if ($s['feedback'] !== ''): ?>
            <div class="mt-6 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm" role="status"><?php echo e($s['feedback']); ?></div>
        <?php endif; ?>
        <?php if ($s['error'] !== ''): ?>
            <div class="mt-6 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm" role="alert"><?php echo e($s['error']); ?></div>
        <?php endif; ?>

        <div class="mt-6 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 border-b border-slate-100 pb-4"><?php echo $editing ? 'Edit ' . e($r['singular']) : 'Add New ' . e($r['singular']); ?></h2>
            <form method="post" enctype="multipart/form-data" class="mt-6 grid md:grid-cols-2 gap-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>">
                <?php endif; ?>

                <?php foreach ($r['fields'] as $name => $f):
                    $type = $f['type'] ?? 'text';
                    $id = 'f-' . $name;
                    $value = (string) ($form[$name] ?? ($f['default'] ?? ''));
                    $wide = !empty($f['wide']) || $type === 'textarea';
                ?>
                    <?php if ($type === 'publish'): ?>
                        <div class="md:col-span-2 flex items-center gap-3">
                            <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo e($name); ?>" value="1" class="w-4 h-4" <?php echo (!$editing && !$form) || !empty($form[$name]) ? 'checked' : ''; ?>>
                            <label for="<?php echo $id; ?>" class="text-sm font-semibold text-slate-700"><?php echo e($f['label']); ?></label>
                        </div>
                        <?php continue; ?>
                    <?php endif; ?>
                    <div class="<?php echo $wide ? 'md:col-span-2' : ''; ?>">
                        <label for="<?php echo $id; ?>" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <?php echo e($f['label']); ?><?php echo !empty($f['required']) ? ' *' : ''; ?>
                            <?php if (!empty($f['hint'])): ?><span class="text-xs font-normal text-slate-500">(<?php echo e($f['hint']); ?>)</span><?php endif; ?>
                        </label>
                        <?php if ($type === 'textarea'): ?>
                            <textarea id="<?php echo $id; ?>" name="<?php echo e($name); ?>" rows="<?php echo (int) ($f['rows'] ?? 4); ?>" <?php echo !empty($f['required']) ? 'required' : ''; ?> class="<?php echo $input; ?> bg-slate-50 focus:bg-white"><?php echo e($value); ?></textarea>
                        <?php elseif ($type === 'select'): ?>
                            <select id="<?php echo $id; ?>" name="<?php echo e($name); ?>" class="<?php echo $input; ?>">
                                <?php foreach ($f['options'] as $optValue => $optLabel): ?>
                                    <option value="<?php echo e($optValue); ?>" <?php echo $value === (string) $optValue ? 'selected' : ''; ?>><?php echo e($optLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="<?php echo e($type); ?>" id="<?php echo $id; ?>" name="<?php echo e($name); ?>"
                                   value="<?php echo e($type === 'time' ? substr($value, 0, 5) : $value); ?>"
                                   <?php echo !empty($f['required']) ? 'required' : ''; ?>
                                   <?php echo !empty($f['max']) ? 'maxlength="' . (int) $f['max'] . '"' : ''; ?>
                                   <?php echo $type === 'url' ? 'placeholder="https://..."' : ''; ?>
                                   class="<?php echo $input; ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($imageField): ?>
                <div class="md:col-span-2 p-5 border border-slate-200 rounded-lg bg-slate-50/50">
                    <span class="block text-sm font-semibold text-slate-700 mb-3"><?php echo e($r['image']['label'] ?? 'Image'); ?></span>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="f-<?php echo e($imageField); ?>" class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">Upload file (JPG, PNG, GIF or WebP, max 20 MB)</label>
                            <input type="file" id="f-<?php echo e($imageField); ?>" name="<?php echo e($imageField); ?>" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border border-slate-300 rounded-lg px-4 py-2 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label for="f-<?php echo e($imageField); ?>-url" class="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wider">Or paste an image link</label>
                            <input type="url" id="f-<?php echo e($imageField); ?>-url" name="<?php echo e($imageField); ?>_url" placeholder="https://..." class="<?php echo $input; ?> bg-white text-sm">
                        </div>
                    </div>
                    <?php if ($editing && !empty($editing[$imageField])): ?>
                        <div class="mt-4 flex items-end gap-6">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 mb-1.5">Current image</span>
                                <img src="<?php echo e(admin_crud_image_src($editing[$imageField])); ?>" alt="" class="h-24 w-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="<?php echo e($imageField); ?>_remove" value="1"> Remove image</label>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="md:col-span-2 pt-4 border-t border-slate-100 flex gap-3">
                    <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-6 py-2.5 text-sm font-semibold transition-all shadow-md shadow-blue-500/30">
                        <?php echo $editing ? 'Update ' . e($r['singular']) : 'Add ' . e($r['singular']); ?>
                    </button>
                    <?php if ($editing): ?>
                        <a href="<?php echo e($r['page']); ?>" class="rounded-lg border border-slate-300 text-slate-700 px-6 py-2.5 text-sm font-semibold hover:bg-slate-50 transition-all text-center">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-6 md:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Existing <?php echo e($r['plural']); ?></h2>
            <?php if (empty($s['rows'])): ?>
                <p class="mt-3 text-sm text-slate-600">None yet. Add the first one above.</p>
            <?php else: ?>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($s['rows'] as $row):
                        $card = $r['card']($row);
                        $img = $imageField ? admin_crud_image_src($row[$imageField] ?? '') : '';
                    ?>
                        <div class="border border-slate-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow flex flex-col">
                            <?php if ($imageField): ?>
                                <?php if ($img !== ''): ?>
                                    <img src="<?php echo e($img); ?>" alt="" class="w-full h-40 object-cover" loading="lazy">
                                <?php else: ?>
                                    <div class="w-full h-40 bg-slate-100 flex items-center justify-center text-slate-400 text-sm">No image</div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-semibold text-slate-900"><?php echo e($card['title']); ?></h3>
                                <?php foreach ($card['meta'] ?? [] as $line): ?>
                                    <p class="mt-1 text-sm text-slate-600"><?php echo e($line); ?></p>
                                <?php endforeach; ?>
                                <?php if (!empty($card['badge'])): ?>
                                    <p class="mt-2"><span class="inline-block px-2 py-0.5 rounded text-[0.65rem] font-bold tracking-wider uppercase border <?php echo e($card['badge'][1]); ?>"><?php echo e($card['badge'][0]); ?></span></p>
                                <?php endif; ?>
                                <div class="mt-auto pt-4 flex gap-2">
                                    <?php if (!empty($card['view'])): ?>
                                        <a href="<?php echo e($card['view']); ?>" target="_blank" rel="noopener" class="rounded border border-slate-300 text-slate-700 text-center px-3 py-2 text-sm hover:bg-slate-50 font-medium">View</a>
                                    <?php endif; ?>
                                    <a href="<?php echo e($r['page']); ?>?edit=<?php echo (int) $row['id']; ?>" class="flex-1 rounded border border-blue-300 text-blue-700 text-center px-3 py-2 text-sm hover:bg-blue-50 font-medium">Edit</a>
                                    <form method="post" onsubmit="return confirm('Delete this <?php echo e(strtolower($r['singular'])); ?>? This cannot be undone.');" class="flex-1">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button type="submit" class="w-full rounded border border-red-300 text-red-700 px-3 py-2 text-sm hover:bg-red-50 font-medium">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($s['totalPages'] > 1): ?>
                <nav class="mt-8 flex justify-center items-center gap-2" aria-label="Pages">
                    <?php if ($s['page'] > 1): ?>
                        <a href="?p=<?php echo $s['page'] - 1; ?>" class="px-4 py-2 border rounded hover:bg-slate-50 text-sm font-medium">Previous</a>
                    <?php endif; ?>
                    <span class="px-4 py-2 text-sm font-medium text-slate-500">Page <?php echo $s['page']; ?> of <?php echo $s['totalPages']; ?></span>
                    <?php if ($s['page'] < $s['totalPages']): ?>
                        <a href="?p=<?php echo $s['page'] + 1; ?>" class="px-4 py-2 border rounded hover:bg-slate-50 text-sm font-medium">Next</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php
}
