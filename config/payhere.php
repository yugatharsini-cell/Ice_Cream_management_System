<?php

// PayHere Sandbox configuration.
// IMPORTANT: Never commit real/live Merchant Secret values to GitHub.

$payhere = [
    'sandbox'       => true,
    'merchant_id'   => '1238244',
    'merchant_secret' => 'MTkyNDc3MDQ3MzM2MzA3Mzk1MjYxNDQ3MDg3NTIyMDE2MDQ4NDY=',
    'checkout_url'  => 'https://sandbox.payhere.lk/pay/checkout',

    // These two URLs can point to localhost because the customer's browser
    // is redirected back to your computer after the payment.
    'return_url'    => 'http://localhost/ice_cream_shop/payment_return.php',
    'cancel_url'    => 'http://localhost/ice_cream_shop/payment_cancel.php',

    // PayHere must be able to reach this URL from the Internet.
    // Replace this with your public tunnel/domain URL before testing.
    // Example: https://xxxx.ngrok-free.app/payment_notify.php
    'notify_url'    => 'https://juice-idly-immortal.ngrok-free.dev/ice_cream_shop/payment_notify.php',

    'currency'      => 'LKR',
];

/**
 * Generate the PayHere Checkout API hash on the server side.
 */
function generate_payhere_hash(
    string $merchantId,
    string $orderId,
    float $amount,
    string $currency,
    string $merchantSecret
): string {
    $formattedAmount = number_format($amount, 2, '.', '');

    return strtoupper(
        md5(
            $merchantId .
            $orderId .
            $formattedAmount .
            $currency .
            strtoupper(md5($merchantSecret))
        )
    );
}
