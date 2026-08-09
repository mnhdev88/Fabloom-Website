<?php
/**
 * Fabloom — single endpoint for every public website form.
 *
 * Handles: contact, linen enquiry, silk enquiry, sample request, home enquiry.
 * Each submission is stored in `enquiries` first, then emailed to every
 * address in MAIL_TO. Storing first means a mail outage never loses a lead.
 *
 * Responds with JSON: {success: bool, message: string, errors?: {field: msg}}
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $payload): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Method not allowed.']);
}

// ── Which form is this? ───────────────────────────────────────
$FORMS = [
    'contact' => ['label' => 'Contact Form',         'require' => ['name', 'email', 'message']],
    'linen'   => ['label' => 'Linen Enquiry',        'require' => ['name', 'email', 'phone']],
    'silk'    => ['label' => 'Silk Enquiry',         'require' => ['name', 'email', 'phone']],
    'sample'  => ['label' => 'Sample Request',       'require' => ['name', 'email', 'phone']],
    'home'    => ['label' => 'Homepage Enquiry',     'require' => ['name', 'email', 'phone']],
];

$type = strtolower(trim((string)($_POST['form_type'] ?? '')));
if (!isset($FORMS[$type])) {
    respond(400, ['success' => false, 'message' => 'Unknown form.']);
}
$form = $FORMS[$type];

// ── Spam controls ─────────────────────────────────────────────
// 1. Honeypot: a field hidden from humans. Anything in it is a bot.
if (trim((string)($_POST['website'] ?? '')) !== '') {
    // Pretend it worked so the bot doesn't retry with a different shape.
    respond(200, ['success' => true, 'message' => 'Thank you — your enquiry has been received.']);
}

// 2. Timing: real people take more than a couple of seconds to fill a form.
$started = (int)($_POST['started_at'] ?? 0);
if ($started > 0 && (time() * 1000 - $started) < 2500) {
    respond(200, ['success' => true, 'message' => 'Thank you — your enquiry has been received.']);
}

// 3. Rate limit per session: 5 submissions per 10 minutes.
$now    = time();
$window = $_SESSION['enquiry_times'] ?? [];
$window = array_values(array_filter($window, static fn($t) => $t > $now - 600));
if (count($window) >= 5) {
    respond(429, [
        'success' => false,
        'message' => 'You have sent several enquiries already. Please try again in a few minutes, or call us on +91 97600 58796.',
    ]);
}

// ── Collect + validate ────────────────────────────────────────
$val = static function (string $key, int $max = 500): string {
    $v = (string)($_POST[$key] ?? '');
    $v = str_replace(["\r\n", "\r"], "\n", trim($v));
    return mb_substr($v, 0, $max);
};

$name    = $val('name', 150);
$email   = $val('email', 190);
$phone   = $val('phone', 40);
$company = $val('company', 190);
$subject = $val('subject', 200);
$message = $val('message', 4000);

$errors = [];
foreach ($form['require'] as $field) {
    if ($$field === '') {
        $errors[$field] = 'This field is required.';
    }
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}
if ($phone !== '' && !preg_match('/^[+\d][\d\s\-()]{6,19}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number.';
}
if ($errors) {
    respond(422, [
        'success' => false,
        'message' => 'Please check the highlighted fields and try again.',
        'errors'  => $errors,
    ]);
}

// Everything else the form sent (fabric specs, product choice, etc.)
$reserved = ['form_type', 'website', 'started_at', 'name', 'email', 'phone', 'company', 'subject', 'message', 'page_url'];

// ucwords() would render these as "Gsm" / "Zari Work"; spell them properly.
$LABELS = [
    'gsm'               => 'GSM',
    'linen_type'        => 'Linen Type',
    'silk_type'         => 'Silk Type',
    'weave_type'        => 'Weave Type',
    'zari_work'         => 'Zari Work',
    'dyeing_preference' => 'Dyeing Preference',
    'product'           => 'Product of Interest',
];

$extra = [];
foreach ($_POST as $k => $v) {
    if (in_array($k, $reserved, true) || !is_string($v)) continue;
    $v = trim($v);
    if ($v === '') continue;
    $label = $LABELS[$k] ?? ucwords(str_replace(['_', '-'], ' ', $k));
    $extra[$label] = mb_substr($v, 0, 500);
}

// ── Optional reference-file attachment ────────────────────────
// The linen/silk enquiry forms offer "Attach Reference File — JPG, PNG,
// PDF, DOC. Max 5MB." Validate against that promise and carry it on the
// notification email.
const MAX_UPLOAD  = 5 * 1024 * 1024;
const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
const ALLOWED_MIME = [
    'image/jpeg', 'image/png', 'application/pdf', 'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];

$attachments = [];
$upload = $_FILES['reference_file'] ?? null;

if ($upload && is_string($upload['tmp_name'] ?? null) && (int)($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $err = (int) $upload['error'];
    if ($err !== UPLOAD_ERR_OK) {
        respond(422, [
            'success' => false,
            'message' => $err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE
                ? 'That file is too large. Please attach a file under 5MB.'
                : 'We could not read that file. Please try attaching it again.',
            'errors'  => ['reference_file' => 'Upload failed.'],
        ]);
    }
    if (!is_uploaded_file($upload['tmp_name'])) {
        respond(400, ['success' => false, 'message' => 'Invalid upload.']);
    }
    if ((int)$upload['size'] > MAX_UPLOAD) {
        respond(422, [
            'success' => false,
            'message' => 'That file is too large. Please attach a file under 5MB.',
            'errors'  => ['reference_file' => 'Maximum size is 5MB.'],
        ]);
    }

    $ext  = strtolower(pathinfo((string)$upload['name'], PATHINFO_EXTENSION));
    $mime = function_exists('finfo_open')
        ? (finfo_file(finfo_open(FILEINFO_MIME_TYPE), $upload['tmp_name']) ?: '')
        : (string)($upload['type'] ?? '');

    if (!in_array($ext, ALLOWED_EXT, true) || ($mime !== '' && !in_array($mime, ALLOWED_MIME, true))) {
        respond(422, [
            'success' => false,
            'message' => 'That file type is not accepted. Please attach a JPG, PNG, PDF or DOC file.',
            'errors'  => ['reference_file' => 'Unsupported file type.'],
        ]);
    }

    $bytes = file_get_contents($upload['tmp_name']);
    if ($bytes !== false) {
        $attachments[] = [
            'name' => basename((string)$upload['name']),
            'type' => $mime ?: 'application/octet-stream',
            'data' => $bytes,
        ];
        $size = strlen($bytes);
        $extra['Attachment'] = basename((string)$upload['name']) . ' ('
            . ($size >= 1048576 ? round($size / 1048576, 1) . ' MB'
             : ($size >= 1024   ? round($size / 1024, 1) . ' KB'
                                : $size . ' bytes')) . ')';
    }
}

$page_url = mb_substr((string)($_POST['page_url'] ?? ($_SERVER['HTTP_REFERER'] ?? '')), 0, 500);
$ip       = mb_substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$agent    = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

// ── 1. Persist ────────────────────────────────────────────────
// db_optional() rather than db(): a database outage must not abort the
// request, because the notification email is the primary delivery path.
$enquiry_id = null;
$pdo = db_optional();
try {
    if ($pdo === null) {
        throw new \PDOException('database unavailable');
    }
    $stmt = $pdo->prepare(
        'INSERT INTO enquiries
            (form_type, name, email, phone, company, subject, message, details, page_url, ip_address, user_agent)
         VALUES (:t, :n, :e, :p, :c, :s, :m, :d, :u, :ip, :ua)'
    );
    $stmt->execute([
        ':t'  => $type,
        ':n'  => $name,
        ':e'  => $email ?: null,
        ':p'  => $phone ?: null,
        ':c'  => $company ?: null,
        ':s'  => $subject ?: null,
        ':m'  => $message ?: null,
        ':d'  => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : null,
        ':u'  => $page_url ?: null,
        ':ip' => $ip ?: null,
        ':ua' => $agent ?: null,
    ]);
    $enquiry_id = (int) $pdo->lastInsertId();
} catch (\PDOException $e) {
    // Don't abort — the email is the primary delivery path. Log and continue.
    error_log('Enquiry insert failed: ' . $e->getMessage());
}

// ── 2. Notify ─────────────────────────────────────────────────
$rows = array_merge(
    array_filter([
        'Name'    => $name,
        'Email'   => $email,
        'Phone'   => $phone,
        'Company' => $company,
        'Subject' => $subject,
    ]),
    $extra,
    array_filter([
        'Message' => $message,
    ])
);

$inner = mail_table($rows)
       . '<p style="margin:16px 0 0;font-size:12px;color:#57534E">'
       . 'Submitted ' . date('d M Y, g:i a')
       . ($page_url !== '' ? ' from ' . htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8') : '')
       . '</p>';

$heading = $form['label'] . ($name !== '' ? ' — ' . $name : '');
$subject_line = '[Fabloom] ' . $form['label'] . ($name !== '' ? ': ' . $name : '');

[$sent, $mail_error] = send_notification(
    $subject_line,
    mail_wrap($heading, $inner, 'Reply directly to this email to respond to ' . ($name ?: 'the sender') . '.'),
    $email,
    $name,
    $attachments
);

if ($enquiry_id !== null && $pdo !== null) {
    try {
        $pdo->prepare('UPDATE enquiries SET mail_sent = :s, mail_error = :e WHERE id = :id')
            ->execute([':s' => $sent ? 1 : 0, ':e' => $sent ? null : $mail_error, ':id' => $enquiry_id]);
    } catch (\PDOException $e) {
        error_log('Enquiry mail-status update failed: ' . $e->getMessage());
    }
}

// The lead is safe if either path worked.
if (!$sent && $enquiry_id === null) {
    respond(500, [
        'success' => false,
        'message' => 'We could not send your enquiry just now. Please email info@thefabloom.com or call +91 97600 58796.',
    ]);
}

$window[] = $now;
$_SESSION['enquiry_times'] = $window;

respond(200, [
    'success' => true,
    'message' => 'Thank you — your enquiry has been received. Our team will be in touch within 24 hours.',
]);
