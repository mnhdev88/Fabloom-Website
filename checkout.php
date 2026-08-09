<?php
/**
 * Fabloom – Checkout Page
 * Requires login. Validates delivery details, places order via PDO transaction.
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

// ── Auth guard ──────────────────────────────────────────────────────────────
require_login('/account/login.php');

// ── Guard: must have cart items ─────────────────────────────────────────────
$items = cart_items();
if (empty($items)) {
    flash('error', 'Your cart is empty. Please add some products before checking out.');
    header('Location: ' . SITE_URL . '/cart');
    exit;
}

$user      = current_user();
$subtotal  = cart_subtotal();
$shipping  = cart_shipping();
$total     = cart_total();
$count     = cart_count();

// ── Process POST ─────────────────────────────────────────────────────────────
$errors = [];
$form   = [
    'name'    => $user['name']  ?? '',
    'email'   => $user['email'] ?? '',
    'phone'   => $user['phone'] ?? '',
    'address' => '',
    'city'    => '',
    'state'   => '',
    'pincode' => '',
    'notes'   => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── CSRF ───────────────────────────────────────────────────────────
    if (!csrf_verify()) {
        $errors[] = 'Security token mismatch. Please refresh the page and try again.';
    } else {

        // ── Sanitise & collect fields ──────────────────────────────────
        $form = [
            'name'    => trim($_POST['name']    ?? ''),
            'email'   => trim($_POST['email']   ?? ''),
            'phone'   => trim($_POST['phone']   ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city'    => trim($_POST['city']    ?? ''),
            'state'   => trim($_POST['state']   ?? ''),
            'pincode' => trim($_POST['pincode'] ?? ''),
            'notes'   => trim($_POST['notes']   ?? ''),
        ];

        // ── Validate required fields ───────────────────────────────────
        if ($form['name'] === '') {
            $errors['name'] = 'Full name is required.';
        } elseif (mb_strlen($form['name']) > 100) {
            $errors['name'] = 'Name must not exceed 100 characters.';
        }

        if ($form['email'] === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($form['address'] === '') {
            $errors['address'] = 'Delivery address is required.';
        } elseif (mb_strlen($form['address']) > 500) {
            $errors['address'] = 'Address must not exceed 500 characters.';
        }

        if ($form['city'] === '') {
            $errors['city'] = 'City is required.';
        } elseif (mb_strlen($form['city']) > 100) {
            $errors['city'] = 'City must not exceed 100 characters.';
        }

        if ($form['state'] === '') {
            $errors['state'] = 'State is required.';
        } elseif (mb_strlen($form['state']) > 100) {
            $errors['state'] = 'State must not exceed 100 characters.';
        }

        if ($form['pincode'] === '') {
            $errors['pincode'] = 'Pincode is required.';
        } elseif (!preg_match('/^\d{6}$/', $form['pincode'])) {
            $errors['pincode'] = 'Please enter a valid 6-digit pincode.';
        }

        // Optional phone validation
        if ($form['phone'] !== '' && !preg_match('/^[\+\d\s\-\(\)]{7,20}$/', $form['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        // Re-calculate shipping server-side (never trust client)
        $subtotal = cart_subtotal();
        $shipping = $subtotal >= FREE_SHIPPING_ABOVE ? 0 : (float) SHIPPING_CHARGE;
        $total    = $subtotal + $shipping;

        // ── Place order if no errors ───────────────────────────────────
        if (empty($errors)) {
            try {
                $db  = db();
                $order_number = generate_order_number();

                $db->beginTransaction();

                // Insert order header
                $stmt = $db->prepare(
                    'INSERT INTO orders
                        (user_id, order_number, status, subtotal, shipping, total,
                         payment_method, payment_status, name, email, phone, address,
                         city, state, pincode, notes)
                     VALUES
                        (:user_id, :order_number, "pending", :subtotal, :shipping, :total,
                         "cod", "pending", :name, :email, :phone, :address,
                         :city, :state, :pincode, :notes)'
                );

                $stmt->execute([
                    ':user_id'      => $user['id'],
                    ':order_number' => $order_number,
                    ':subtotal'     => $subtotal,
                    ':shipping'     => $shipping,
                    ':total'        => $total,
                    ':name'         => $form['name'],
                    ':email'        => $form['email'],
                    ':phone'        => $form['phone'] !== '' ? $form['phone'] : null,
                    ':address'      => $form['address'],
                    ':city'         => $form['city'],
                    ':state'        => $form['state'],
                    ':pincode'      => $form['pincode'],
                    ':notes'        => $form['notes'] !== '' ? $form['notes'] : null,
                ]);

                $order_id = (int) $db->lastInsertId();

                // Insert order items
                $item_stmt = $db->prepare(
                    'INSERT INTO order_items (order_id, product_id, name, price, quantity)
                     VALUES (:order_id, :product_id, :name, :price, :quantity)'
                );

                foreach ($items as $pid => $item) {
                    $item_stmt->execute([
                        ':order_id'   => $order_id,
                        ':product_id' => (int) $item['id'],
                        ':name'       => $item['name'],
                        ':price'      => $item['price'],
                        ':quantity'   => (int) $item['qty'],
                    ]);
                }

                $db->commit();

                // ── Notify the team (info@ + fabloom86@) ──────────────
                // Outside the transaction: a mail hiccup must never roll
                // back an order that is already committed.
                try {
                    $lines = '';
                    foreach ($items as $item) {
                        $lines .= '<tr>'
                            . '<td style="padding:6px 10px;border-bottom:1px solid #E8E2D9">'
                            . htmlspecialchars((string)$item['name'], ENT_QUOTES, 'UTF-8') . '</td>'
                            . '<td style="padding:6px 10px;border-bottom:1px solid #E8E2D9;text-align:center">'
                            . (int)$item['qty'] . '</td>'
                            . '<td style="padding:6px 10px;border-bottom:1px solid #E8E2D9;text-align:right">'
                            . htmlspecialchars(fmt_price((float)$item['price'] * (int)$item['qty']), ENT_QUOTES, 'UTF-8')
                            . '</td></tr>';
                    }

                    $inner = mail_table([
                        'Order No.'  => $order_number,
                        'Customer'   => $form['name'],
                        'Email'      => $form['email'],
                        'Phone'      => $form['phone'],
                        'Ship to'    => $form['address'] . "\n" . $form['city'] . ', ' . $form['state'] . ' ' . $form['pincode'],
                        'Payment'    => 'Cash on Delivery',
                        'Notes'      => $form['notes'],
                    ])
                    . '<table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;margin-top:16px;border-collapse:collapse;font:14px/1.6 Arial,Helvetica,sans-serif">'
                    . '<tr><th align="left" style="padding:6px 10px;background:#FAF8F5;border-bottom:2px solid #C0282A">Item</th>'
                    . '<th style="padding:6px 10px;background:#FAF8F5;border-bottom:2px solid #C0282A">Qty</th>'
                    . '<th align="right" style="padding:6px 10px;background:#FAF8F5;border-bottom:2px solid #C0282A">Amount</th></tr>'
                    . $lines
                    . '<tr><td colspan="2" align="right" style="padding:6px 10px">Subtotal</td>'
                    . '<td align="right" style="padding:6px 10px">' . htmlspecialchars(fmt_price($subtotal), ENT_QUOTES, 'UTF-8') . '</td></tr>'
                    . '<tr><td colspan="2" align="right" style="padding:6px 10px">Shipping</td>'
                    . '<td align="right" style="padding:6px 10px">' . htmlspecialchars($shipping > 0 ? fmt_price($shipping) : 'Free', ENT_QUOTES, 'UTF-8') . '</td></tr>'
                    . '<tr><td colspan="2" align="right" style="padding:8px 10px;font-weight:700;border-top:2px solid #C0282A">Total</td>'
                    . '<td align="right" style="padding:8px 10px;font-weight:700;border-top:2px solid #C0282A">'
                    . htmlspecialchars(fmt_price($total), ENT_QUOTES, 'UTF-8') . '</td></tr></table>';

                    send_notification(
                        '[Fabloom] New order ' . $order_number . ' — ' . fmt_price($total),
                        mail_wrap('New Order ' . $order_number, $inner, 'Reply to this email to contact the customer.'),
                        (string)$form['email'],
                        (string)$form['name']
                    );
                } catch (\Throwable $e) {
                    error_log('Order notification failed for ' . $order_number . ': ' . $e->getMessage());
                }

                // Clear cart after successful order
                cart_clear();

                // Redirect to confirmation page
                header('Location: ' . SITE_URL . '/order-success?order=' . urlencode($order_number));
                exit;

            } catch (\PDOException $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log('Order placement failed: ' . $e->getMessage());
                $errors[] = 'We could not process your order at this time. Please try again or contact support.';
            }
        }
    }
}

$page_title = 'Checkout – Fabloom';
$page_desc  = 'Complete your Fabloom order. Secure checkout with Cash on Delivery.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page Hero ──────────────────────────────────────────────────────── -->
<section class="page-hero" aria-labelledby="checkout-hero-title">
  <div class="container page-hero__inner">
    <span class="page-hero__label">Secure Checkout</span>
    <h1 class="page-hero__title" id="checkout-hero-title">Complete Your Order</h1>
    <p class="page-hero__subtitle">
      <?= $count ?> <?= $count === 1 ? 'item' : 'items' ?> · <?= h(fmt_price($total)) ?> total
    </p>
    <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <span class="sep" aria-hidden="true">›</span>
      <a href="<?= SITE_URL ?>/cart">Cart</a>
      <span class="sep" aria-hidden="true">›</span>
      <span class="current" aria-current="page">Checkout</span>
    </nav>
  </div>
</section>

<!-- ── Checkout Content ────────────────────────────────────────────────── -->
<section class="section section--sm" aria-label="Checkout form">
  <div class="container">
    <div class="shop-layout">

      <!-- ── Checkout Form ──────────────────────────────────────────── -->
      <div class="shop-layout__main">
        <div class="checkout-form-card">

          <!-- Card header -->
          <div class="checkout-form-card__header">
            <span class="checkout-step-badge" aria-hidden="true">1</span>
            <h2>Delivery &amp; Contact Details</h2>
          </div>

          <div class="checkout-form-body">

            <!-- ── Global validation errors ─────────────────────────── -->
            <?php if (!empty($errors)): ?>
            <div class="form-alert form-alert--error" role="alert" aria-live="assertive">
              <svg class="form-alert__icon" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              </svg>
              <div class="form-alert__content">
                <p class="form-alert__title">Please fix the following before continuing:</p>
                <ul class="form-alert__list">
                  <?php foreach ($errors as $err): ?>
                  <li><?= h(is_string($err) ? $err : (string) $err) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= h(SITE_URL . '/checkout') ?>"
                  novalidate aria-label="Checkout form" id="checkout-form">
              <?= csrf_field() ?>

              <!-- ── Contact Info ─────────────────────────────────── -->
              <p class="form-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Contact Information
              </p>

              <div class="form-grid" style="margin-bottom:var(--sp-6)">
                <div class="form-group">
                  <label for="name" class="form-label">
                    Full Name <span class="req" aria-hidden="true">*</span>
                  </label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control <?= isset($errors['name']) ? 'form-control--error' : '' ?>"
                    value="<?= h($form['name']) ?>"
                    required
                    autocomplete="name"
                    maxlength="100"
                    aria-describedby="<?= isset($errors['name']) ? 'name-error' : '' ?>"
                    aria-required="true"
                    placeholder="Your full name">
                  <?php if (isset($errors['name'])): ?>
                  <p id="name-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['name']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="email" class="form-label">
                    Email Address <span class="req" aria-hidden="true">*</span>
                  </label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control <?= isset($errors['email']) ? 'form-control--error' : '' ?>"
                    value="<?= h($form['email']) ?>"
                    required
                    autocomplete="email"
                    maxlength="150"
                    aria-describedby="<?= isset($errors['email']) ? 'email-error' : '' ?>"
                    aria-required="true"
                    placeholder="you@example.com">
                  <?php if (isset($errors['email'])): ?>
                  <p id="email-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['email']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group form-col-full">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control <?= isset($errors['phone']) ? 'form-control--error' : '' ?>"
                    value="<?= h($form['phone']) ?>"
                    autocomplete="tel"
                    maxlength="20"
                    aria-describedby="<?= isset($errors['phone']) ? 'phone-error' : '' ?>"
                    placeholder="+91 00000 00000">
                  <?php if (isset($errors['phone'])): ?>
                  <p id="phone-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['phone']) ?>
                  </p>
                  <?php endif; ?>
                </div>
              </div>

              <!-- ── Delivery Address ─────────────────────────────── -->
              <p class="form-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                Delivery Address
              </p>

              <div class="form-grid" style="margin-bottom:var(--sp-6)">
                <div class="form-group form-col-full">
                  <label for="address" class="form-label">
                    Street Address <span class="req" aria-hidden="true">*</span>
                  </label>
                  <textarea
                    id="address"
                    name="address"
                    class="form-control <?= isset($errors['address']) ? 'form-control--error' : '' ?>"
                    required
                    rows="3"
                    maxlength="500"
                    autocomplete="street-address"
                    aria-describedby="<?= isset($errors['address']) ? 'address-error' : '' ?>"
                    aria-required="true"
                    placeholder="House / Flat no., Street, Locality, Landmark"><?= h($form['address']) ?></textarea>
                  <?php if (isset($errors['address'])): ?>
                  <p id="address-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['address']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="city" class="form-label">
                    City <span class="req" aria-hidden="true">*</span>
                  </label>
                  <input
                    type="text"
                    id="city"
                    name="city"
                    class="form-control <?= isset($errors['city']) ? 'form-control--error' : '' ?>"
                    value="<?= h($form['city']) ?>"
                    required
                    autocomplete="address-level2"
                    maxlength="100"
                    aria-describedby="<?= isset($errors['city']) ? 'city-error' : '' ?>"
                    aria-required="true"
                    placeholder="e.g. Patna">
                  <?php if (isset($errors['city'])): ?>
                  <p id="city-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['city']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="state" class="form-label">
                    State <span class="req" aria-hidden="true">*</span>
                  </label>
                  <select
                    id="state"
                    name="state"
                    class="form-control <?= isset($errors['state']) ? 'form-control--error' : '' ?>"
                    required
                    autocomplete="address-level1"
                    aria-describedby="<?= isset($errors['state']) ? 'state-error' : '' ?>"
                    aria-required="true">
                    <option value="">-- Select State --</option>
                    <?php
                    $states = [
                      'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
                      'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
                      'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
                      'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana',
                      'Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                      'Andaman & Nicobar Islands','Chandigarh','Dadra & Nagar Haveli and Daman & Diu',
                      'Delhi','Jammu & Kashmir','Ladakh','Lakshadweep','Puducherry',
                    ];
                    foreach ($states as $state):
                    ?>
                    <option value="<?= h($state) ?>"
                      <?= $form['state'] === $state ? 'selected' : '' ?>>
                      <?= h($state) ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (isset($errors['state'])): ?>
                  <p id="state-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['state']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group">
                  <label for="pincode" class="form-label">
                    Pincode <span class="req" aria-hidden="true">*</span>
                  </label>
                  <input
                    type="text"
                    id="pincode"
                    name="pincode"
                    class="form-control <?= isset($errors['pincode']) ? 'form-control--error' : '' ?>"
                    value="<?= h($form['pincode']) ?>"
                    required
                    autocomplete="postal-code"
                    maxlength="6"
                    pattern="\d{6}"
                    inputmode="numeric"
                    aria-describedby="<?= isset($errors['pincode']) ? 'pincode-error' : '' ?>"
                    aria-required="true"
                    placeholder="6-digit pincode">
                  <?php if (isset($errors['pincode'])): ?>
                  <p id="pincode-error" class="form-error-msg" role="alert">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                    <?= h($errors['pincode']) ?>
                  </p>
                  <?php endif; ?>
                </div>

                <div class="form-group form-col-full">
                  <label for="notes" class="form-label">Order Notes <span style="font-weight:400;color:var(--clr-warm-gray)">(optional)</span></label>
                  <textarea
                    id="notes"
                    name="notes"
                    class="form-control"
                    rows="2"
                    maxlength="1000"
                    placeholder="Special delivery instructions, fabric preferences, gift messages…"><?= h($form['notes']) ?></textarea>
                </div>
              </div>

              <!-- ── Payment Method ───────────────────────────────── -->
              <p class="form-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                  <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                Payment Method
              </p>

              <div class="payment-methods" style="margin-bottom:var(--sp-8)" role="radiogroup" aria-label="Choose payment method">

                <!-- Cash on Delivery (active) -->
                <label class="payment-option payment-option--active" for="pay_cod">
                  <input type="radio" id="pay_cod" name="payment_method" value="cod" checked
                         aria-describedby="cod-desc">
                  <div class="payment-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                      <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>
                    </svg>
                  </div>
                  <div class="payment-option__text">
                    <p class="payment-name">Cash on Delivery</p>
                    <p class="payment-desc" id="cod-desc">Pay in cash when your order arrives at your door.</p>
                  </div>
                </label>

                <!-- Online Payment (coming soon) -->
                <label class="payment-option payment-option--disabled" for="pay_online"
                       aria-disabled="true">
                  <input type="radio" id="pay_online" name="payment_method" value="online"
                         disabled aria-disabled="true">
                  <div class="payment-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                      <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                  </div>
                  <div class="payment-option__text">
                    <p class="payment-name">Online Payment</p>
                    <p class="payment-desc">UPI, Net Banking, Credit / Debit Card</p>
                  </div>
                  <span class="payment-coming-soon" aria-label="Coming soon">Coming Soon</span>
                </label>

                <!-- UPI (coming soon) -->
                <label class="payment-option payment-option--disabled" for="pay_upi"
                       aria-disabled="true">
                  <input type="radio" id="pay_upi" name="payment_method" value="upi"
                         disabled aria-disabled="true">
                  <div class="payment-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                      <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                  </div>
                  <div class="payment-option__text">
                    <p class="payment-name">UPI / QR Code</p>
                    <p class="payment-desc">GPay, PhonePe, Paytm and more</p>
                  </div>
                  <span class="payment-coming-soon" aria-label="Coming soon">Coming Soon</span>
                </label>

              </div>

              <!-- ── Submit ──────────────────────────────────────────── -->
              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"
                      id="place-order-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                Place Order · <?= h(fmt_price($total)) ?>
              </button>

              <p style="text-align:center;font-size:var(--text-xs);color:var(--clr-text-muted);margin-top:var(--sp-4)">
                By placing your order you agree to our
                <a href="<?= SITE_URL ?>/terms-and-conditions" style="color:var(--clr-red)">Terms &amp; Conditions</a>
                and
                <a href="<?= SITE_URL ?>/privacy-policy" style="color:var(--clr-red)">Privacy Policy</a>.
              </p>

            </form>
          </div><!-- /.checkout-form-body -->
        </div><!-- /.checkout-form-card -->
      </div><!-- /.shop-layout__main -->

      <!-- ── Order Summary Sidebar ───────────────────────────────────── -->
      <aside class="shop-layout__sidebar" aria-label="Order summary">
        <div class="order-summary">
          <div class="order-summary__header">
            <h3>Your Order</h3>
          </div>
          <div class="order-summary__body">

            <!-- Items list -->
            <div class="order-items-list" aria-label="Cart items">
              <?php foreach ($items as $pid => $item): ?>
              <?php $img_src = product_img($item['image'] ?? ''); ?>
              <div class="order-item-row">
                <div class="order-item-thumb">
                  <img
                    src="<?= h($img_src) ?>"
                    alt="<?= h($item['name']) ?>"
                    loading="lazy"
                    width="52" height="52"
                    onerror="this.src='<?= SITE_URL ?>/assets/images/linen-fabric-hero.webp'">
                  <span class="order-item-qty-badge" aria-label="Quantity: <?= (int) $item['qty'] ?>">
                    <?= (int) $item['qty'] ?>
                  </span>
                </div>
                <span class="order-item-name" title="<?= h($item['name']) ?>">
                  <?= h($item['name']) ?>
                </span>
                <span class="order-item-price">
                  <?= h(fmt_price($item['price'] * $item['qty'])) ?>
                </span>
              </div>
              <?php endforeach; ?>
            </div>

            <!-- Totals -->
            <div class="order-summary__row">
              <span class="order-summary__row--label">Subtotal</span>
              <span class="order-summary__row--value"><?= h(fmt_price($subtotal)) ?></span>
            </div>
            <div class="order-summary__row <?= $shipping === 0.0 ? 'order-summary__row--shipping-free' : '' ?>">
              <span class="order-summary__row--label">Shipping</span>
              <span class="order-summary__row--value">
                <?php if ($shipping === 0.0): ?>
                  <span class="free-ship-badge">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Free
                  </span>
                <?php else: ?>
                  <span class="ship-cost-badge"><?= h(fmt_price($shipping)) ?></span>
                <?php endif; ?>
              </span>
            </div>

            <div class="order-summary__divider" aria-hidden="true"></div>

            <div class="order-summary__total-row">
              <span class="order-summary__total-label">Total</span>
              <span class="order-summary__total-amount"><?= h(fmt_price($total)) ?></span>
            </div>
          </div>

          <div class="order-summary__footer">
            <p class="order-summary__note">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
              Cash on Delivery available
            </p>
            <p class="order-summary__note">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              Secure &amp; encrypted checkout
            </p>
            <a href="<?= SITE_URL ?>/cart" class="btn btn-outline" style="justify-content:center">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
              Edit Cart
            </a>
          </div>
        </div>
      </aside>

    </div><!-- /.shop-layout -->
  </div>
</section>

<script>
(function () {
  'use strict';

  // Disable submit button while form is submitting to prevent double-clicks
  var form = document.getElementById('checkout-form');
  var btn  = document.getElementById('place-order-btn');
  if (form && btn) {
    form.addEventListener('submit', function () {
      btn.disabled = true;
      btn.textContent = 'Placing Order…';
      btn.style.opacity = '0.7';
    });
  }

  // Highlight active payment option on click
  document.querySelectorAll('.payment-option input[type="radio"]:not(:disabled)').forEach(function (radio) {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.payment-option').forEach(function (opt) {
        opt.classList.remove('payment-option--active');
      });
      radio.closest('.payment-option').classList.add('payment-option--active');
    });
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
