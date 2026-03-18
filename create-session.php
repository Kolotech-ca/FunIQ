<?php
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey(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);

$product_id = $_POST['product_id'] ?? null;
$success_url = 'https://funiq.ca/success.html?session_id={CHECKOUT_SESSION_ID}';
$cancel_url = 'cancel_url' => 'https://funiq.ca/cancel.html?product_id=';

if (!$product_id) {
    die("Product not specified.");
}

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => $product_id, // Stripe Price ID
            'quantity' => 1
        ]],
        'mode' => 'payment',
        'success_url' => $success_url,
        'cancel_url' => $cancel_url. $product_id,
        'metadata' => [
            'product_id' => $product_id
        ],
    ]);

    header("Location: " . $session->url);
    exit;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>