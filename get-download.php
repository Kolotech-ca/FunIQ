<?php
require 'vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

header('Content-Type: application/json');

$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    echo json_encode(['error' => 'No session ID']);
    exit;
}

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    $product_id = $session->metadata->product_id ?? null;

    global $product_to_file;

    if ($product_id && isset($product_to_file[$product_id])) {
        $fileId = $product_to_file[$product_id];
        $downloadLink = "https://drive.google.com/uc?export=download&id=$fileId";

        echo json_encode(['success' => true, 'link' => $downloadLink]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>