<?php
require 'vendor/autoload.php';
require 'config.php';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, STRIPE_WEBHOOK_SECRET);
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

if ($event->type == 'checkout.session.completed') {
    $session = $event->data->object;

    $product_id = $session->metadata->product_id ?? null;
    $customer_email = $session->customer_email ?? null;

    global $product_to_file;

    if ($product_id && isset($product_to_file[$product_id]) && $customer_email) {
        $fileId = $product_to_file[$product_id];
        $downloadLink = "https://drive.google.com/uc?export=download&id=$fileId";

        // Send email to customer
        $subject = "Your Download Link";
        $message = "Thank you for your purchase!\nDownload your file here:\n$downloadLink";
        $headers = "From: " . FROM_EMAIL;

        mail($customer_email, $subject, $message, $headers);
    }
}

http_response_code(200);
?>