<?php
/**
 * Admin layout helper — included by admin pages.
 * Call admin_head($title) to open HTML, admin_sidebar($active) for sidebar,
 * then admin_foot() to close.
 *
 * Or just use the inline approach in each file (simpler for small panels).
 * This file is intentionally left minimal — each admin page uses the
 * functions below to avoid duplicating the shell.
 */

function admin_logout_url(): string {
    return SITE_URL . '/admin/logout';
}

/**
 * Emit the opening HTML shell up to <main>.
 * $active: 'dashboard' | 'products' | 'orders'
 */
function admin_html_open(string $title, string $active = ''): void {
    $flash_success = get_flash('success');
    $flash_error   = get_flash('error');
    $admin_name    = h($_SESSION['admin_name'] ?? 'Admin');
    $nav = [
        'dashboard' => ['label' => 'Dashboard',  'href' => SITE_URL . '/admin/index',    'icon' => '&#9732;'],
        'products'  => ['label' => 'Products',   'href' => SITE_URL . '/admin/products', 'icon' => '&#9635;'],
        'orders'    => ['label' => 'Orders',     'href' => SITE_URL . '/admin/orders',   'icon' => '&#9638;'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title) ?> — Fabloom Admin</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f4f5;
            color: #1C1917;
            display: flex;
            min-height: 100vh;
            font-size: 14px;
        }
        /* ── Sidebar ── */
        #sidebar {
            width: 220px;
            min-height: 100vh;
            background: #1C1917;
            color: #d6d3d1;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
        }
        .sb-logo {
            padding: 1.4rem 1.2rem 1.1rem;
            border-bottom: 1px solid #292524;
        }
        .sb-logo-text {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.3px;
        }
        .sb-logo-text span { color: #C0282A; }
        .sb-logo-sub {
            font-size: .65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #78716c;
            margin-top: 1px;
        }
        nav.sb-nav { padding: 1rem 0; flex: 1; }
        nav.sb-nav a {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .6rem 1.2rem;
            color: #a8a29e;
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: color .15s, background .15s;
        }
        nav.sb-nav a:hover { color: #fff; background: #292524; }
        nav.sb-nav a.active {
            color: #fff;
            background: #292524;
            border-left-color: #C0282A;
        }
        nav.sb-nav .nav-icon { font-size: 1rem; opacity: .8; }
        .sb-divider {
            border: none;
            border-top: 1px solid #292524;
            margin: .5rem 0;
        }
        .sb-soon {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .6rem 1.2rem;
            color: #57534e;
            font-size: .85rem;
            cursor: default;
        }
        .sb-soon .badge {
            font-size: .6rem;
            background: #292524;
            color: #78716c;
            border-radius: 3px;
            padding: 1px 5px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .sb-bottom {
            padding: .75rem 0;
            border-top: 1px solid #292524;
        }
        .sb-user {
            padding: .6rem 1.2rem;
            font-size: .78rem;
            color: #78716c;
        }
        /* ── Main content ── */
        #main {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: .85rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h1 { font-size: 1.1rem; font-weight: 700; color: #1C1917; }
        .topbar-right { font-size: .82rem; color: #6b7280; }
        .content { padding: 1.5rem 1.75rem; flex: 1; }
        /* ── Flash messages ── */
        .flash {
            padding: .75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            font-size: .875rem;
            font-weight: 500;
        }
        .flash-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .flash-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }
        /* ── Cards / Stats ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.2rem 1.4rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }
        .stat-card .stat-label {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #6b7280;
            margin-bottom: .4rem;
        }
        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1C1917;
            line-height: 1.1;
        }
        .stat-card .stat-accent { color: #C0282A; }
        /* ── Tables ── */
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .card-header {
            padding: .9rem 1.25rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h2 {
            font-size: .9rem;
            font-weight: 700;
            color: #1C1917;
        }
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
        }
        th {
            background: #f9fafb;
            text-align: left;
            padding: .6rem .9rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            border-bottom: 1px solid #f3f4f6;
        }
        td {
            padding: .65rem .9rem;
            border-bottom: 1px solid #f9fafb;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }
        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .9rem;
            border-radius: 5px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: background .15s;
            line-height: 1.4;
        }
        .btn-primary { background: #C0282A; color: #fff; }
        .btn-primary:hover { background: #a81f21; }
        .btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .btn-secondary:hover { background: #e5e7eb; }
        .btn-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: .3rem .65rem; font-size: .78rem; }
        /* ── Status badges ── */
        .badge {
            display: inline-block;
            padding: .2rem .55rem;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .badge-pending    { background: #fef9c3; color: #713f12; }
        .badge-processing { background: #dbeafe; color: #1e40af; }
        .badge-shipped    { background: #f3e8ff; color: #6b21a8; }
        .badge-delivered  { background: #dcfce7; color: #166534; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }
        .badge-active     { background: #dcfce7; color: #166534; }
        .badge-inactive   { background: #f3f4f6; color: #6b7280; }
        /* ── Form controls ── */
        select, input[type="text"], input[type="number"],
        input[type="email"], input[type="url"], textarea {
            border: 1.5px solid #d1d5db;
            border-radius: 5px;
            padding: .45rem .7rem;
            font-size: .875rem;
            color: #1C1917;
            font-family: inherit;
            outline: none;
            transition: border-color .2s;
            background: #fff;
        }
        select:focus, input:focus, textarea:focus { border-color: #C0282A; }
        textarea { resize: vertical; }
        /* ── Misc ── */
        .thumb {
            width: 42px; height: 42px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .text-muted { color: #9ca3af; }
        .text-right { text-align: right; }
        .mt-1 { margin-top: .5rem; }
        .mt-2 { margin-top: 1rem; }
        .gap-1 { gap: .5rem; }
        .flex { display: flex; align-items: center; }
        .justify-between { justify-content: space-between; }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #374151;
            margin-bottom: .35rem;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
        }
        .form-group textarea { min-height: 80px; }
        .form-check { display: flex; align-items: center; gap: .5rem; }
        .form-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: #C0282A; }
        .section-title {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin: 1.5rem 0 .75rem;
            padding-bottom: .4rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .alert {
            padding: .75rem 1rem;
            border-radius: 6px;
            font-size: .85rem;
            margin-bottom: 1rem;
        }
        .alert-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
        .filter-bar {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .filter-bar label { font-size: .8rem; font-weight: 600; color: #374151; }
        .img-placeholder {
            width: 42px; height: 42px;
            background: #f3f4f6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            color: #9ca3af;
            border: 1px solid #e5e7eb;
        }
        .toggle-yes { color: #C0282A; font-weight: 700; cursor: pointer; }
        .toggle-no  { color: #9ca3af; cursor: pointer; }
        @media (max-width: 768px) {
            #sidebar { width: 200px; }
            #main { margin-left: 200px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div id="sidebar">
    <div class="sb-logo">
        <div class="sb-logo-text">Fab<span>loom</span></div>
        <div class="sb-logo-sub">Admin Panel</div>
    </div>
    <nav class="sb-nav">
        <?php foreach ($nav as $key => $item): ?>
        <a href="<?= h($item['href']) ?>" class="<?= $active === $key ? 'active' : '' ?>">
            <span class="nav-icon"><?= $item['icon'] ?></span>
            <?= h($item['label']) ?>
        </a>
        <?php endforeach; ?>
        <hr class="sb-divider">
        <span class="sb-soon"><span class="nav-icon">&#9654;</span> Users <span class="badge">Soon</span></span>
        <hr class="sb-divider">
        <a href="<?= h(SITE_URL) ?>" target="_blank">&#8599; View Site</a>
    </nav>
    <div class="sb-bottom">
        <div class="sb-user">Signed in as<br><strong style="color:#d6d3d1"><?= $admin_name ?></strong></div>
        <a href="<?= h(admin_logout_url()) ?>" style="display:flex;align-items:center;gap:.5rem;padding:.5rem 1.2rem;color:#ef4444;text-decoration:none;font-size:.82rem;font-weight:600;">
            &#10005; Logout
        </a>
    </div>
</div>
<div id="main">
    <div class="topbar">
        <h1><?= h($title) ?></h1>
        <div class="topbar-right"><?= date('l, d M Y') ?></div>
    </div>
    <div class="content">
        <?php if ($flash_success): ?>
            <div class="flash flash-success"><?= h($flash_success) ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
            <div class="flash flash-error"><?= h($flash_error) ?></div>
        <?php endif; ?>
    <?php
}

function admin_html_close(): void {
    ?>
    </div><!-- .content -->
</div><!-- #main -->
</body>
</html>
    <?php
}
