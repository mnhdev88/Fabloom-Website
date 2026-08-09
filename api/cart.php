<?php
/**
 * Fabloom – Cart API Handler
 * Handles AJAX / form-POST cart actions: add · update · remove · clear
 *
 * All mutations require a valid CSRF token (POST field or X-CSRF-Token header).
 * If the request carries Accept: application/json → always responds with JSON.
 * Otherwise → redirects back to cart.php (progressive-enhancement fallback).
 */

declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

/* ─── Helper: decide response mode ──────────────────────────────────────── */
function wants_json(): bool {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return str_contains($accept, 'application/json')
        || str_contains($accept, '*/*') && !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
}

function json_response(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function redirect_cart(string $message = '', string $type = 'success'): never {
    if ($message !== '') {
        flash($type, $message);
    }
    header('Location: ' . SITE_URL . '/cart.php');
    exit;
}

/* ─── Only allow POST (GET retained for remove as legacy fallback) ───────── */
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

/* ─── CSRF verification ─────────────────────────────────────────────────── */
// Accept token from POST body or from X-CSRF-Token header (for fetch() calls)
$posted_token  = $_POST['csrf_token'] ?? '';
$header_token  = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$provided_token = $posted_token ?: $header_token;
$valid_csrf    = !empty($provided_token) && hash_equals(csrf_token(), $provided_token);

if (!$valid_csrf) {
    if (wants_json()) {
        json_response(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.'], 403);
    }
    flash('error', 'Security token mismatch. Please try again.');
    redirect_cart('', 'error');
}

/* ─── Route actions ──────────────────────────────────────────────────────── */
switch ($action) {

    /* ── ADD ─────────────────────────────────────────────────────────── */
    case 'add':
        if ($method !== 'POST') {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
            }
            redirect_cart('Invalid request method.', 'error');
        }

        $product_id = (int) ($_POST['product_id'] ?? 0);
        $qty        = max(1, (int) ($_POST['qty'] ?? 1));

        // The 50 m minimum has to be enforced here, not just in the form:
        // anything client-side can be posted around.
        $qty = enforce_min_qty($product_id, $qty);

        if ($product_id <= 0) {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Invalid product.'], 422);
            }
            redirect_cart('Invalid product specified.', 'error');
        }

        // cart_add() silently aborts if product doesn't exist or is inactive
        $count_before = cart_count();
        cart_add($product_id, $qty);
        $count_after  = cart_count();

        if ($count_after === $count_before && !isset($_SESSION['cart'][$product_id])) {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Product not found or unavailable.'], 404);
            }
            redirect_cart('Product not available.', 'error');
        }

        $msg = 'Item added to your cart.';

        if (wants_json()) {
            json_response([
                'success'    => true,
                'message'    => $msg,
                'cart_count' => cart_count(),
                'cart_total' => fmt_price(cart_total()),
            ]);
        }

        flash('success', $msg);
        // Redirect to cart after add (non-AJAX)
        header('Location: ' . SITE_URL . '/cart.php');
        exit;

    /* ── UPDATE ──────────────────────────────────────────────────────── */
    case 'update':
        if ($method !== 'POST') {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
            }
            redirect_cart('Invalid request method.', 'error');
        }

        $product_id = (int) ($_POST['product_id'] ?? 0);
        $qty        = (int) ($_POST['qty'] ?? 0);

        if ($product_id <= 0) {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Invalid product.'], 422);
            }
            redirect_cart('Invalid product specified.', 'error');
        }

        // 0 still means "remove"; anything above that is raised to the minimum.
        if ($qty > 0) {
            $qty = enforce_min_qty($product_id, $qty);
        }

        cart_update($product_id, $qty);

        $msg = $qty <= 0
            ? 'Item removed from cart.'
            : 'Cart updated.';

        if (wants_json()) {
            json_response([
                'success'      => true,
                'message'      => $msg,
                'cart_count'   => cart_count(),
                'cart_subtotal'=> fmt_price(cart_subtotal()),
                'cart_shipping'=> cart_shipping() === 0.0 ? 'Free' : fmt_price(cart_shipping()),
                'cart_total'   => fmt_price(cart_total()),
            ]);
        }

        flash('success', $msg);
        redirect_cart();

    /* ── REMOVE ──────────────────────────────────────────────────────── */
    case 'remove':
        // Accept POST (preferred) or GET (legacy link fallback)
        $product_id = (int) ($_POST['product_id'] ?? $_GET['product_id'] ?? 0);

        if ($product_id <= 0) {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Invalid product.'], 422);
            }
            redirect_cart('Invalid product specified.', 'error');
        }

        cart_remove($product_id);

        $msg = 'Item removed from your cart.';

        if (wants_json()) {
            json_response([
                'success'    => true,
                'message'    => $msg,
                'cart_count' => cart_count(),
                'cart_total' => fmt_price(cart_total()),
            ]);
        }

        flash('success', $msg);
        redirect_cart();

    /* ── CLEAR ───────────────────────────────────────────────────────── */
    case 'clear':
        if ($method !== 'POST') {
            if (wants_json()) {
                json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
            }
            redirect_cart('Invalid request method.', 'error');
        }

        cart_clear();

        $msg = 'Your cart has been cleared.';

        if (wants_json()) {
            json_response([
                'success'    => true,
                'message'    => $msg,
                'cart_count' => 0,
                'cart_total' => fmt_price(0),
            ]);
        }

        flash('success', $msg);
        redirect_cart();

    /* ── UNKNOWN ─────────────────────────────────────────────────────── */
    default:
        if (wants_json()) {
            json_response(['success' => false, 'message' => 'Unknown action.'], 400);
        }
        redirect_cart('Unknown action.', 'error');
}
