# PayHere Sandbox Testing Guide

This project uses the PayHere Checkout API in **Sandbox mode**. Sandbox payments are simulated and do not charge real money.

## 1. Configure the project

Open:

```text
config/payhere.php
```

Replace:

```php
'merchant_id' => 'YOUR_SANDBOX_MERCHANT_ID',
'merchant_secret' => 'YOUR_SANDBOX_MERCHANT_SECRET',
```

with the credentials from your PayHere Sandbox merchant account.

Also replace:

```php
'notify_url' => 'https://YOUR-PUBLIC-URL/payment_notify.php',
```

with a publicly accessible URL for `payment_notify.php`.

PayHere requires the `notify_url` to be reachable from the Internet for server-to-server payment notifications. A normal localhost URL cannot receive this callback directly.

## 2. Test payment cards

According to the PayHere Sandbox testing documentation, the following cards can be used for simulated successful payments:

| Card | Test Number |
|---|---|
| Visa | `4916217501611292` |
| MasterCard | `5307732125531191` |
| AMEX | `346781005510225` |

For the card name, CVV, and expiry date, PayHere says any valid values can be used for the successful test cards.

Other cards can be used to test failure scenarios, such as insufficient funds, limit exceeded, do not honor, and network error.

## 3. Payment flow in this project

```text
Customer
   ↓
Checkout
   ↓
Create Pending Order + Pending Payment
   ↓
PayHere Sandbox
   ↓
Customer enters Sandbox test card
   ↓
PayHere sends notification to payment_notify.php
   ↓
Checksum verification
   ↓
Payment status updated
   ↓
Successful payment → Stock reduced → Order Confirmed
```

## 4. Important security point

The project does not collect or store card number, CVV, or expiry date. Those details are entered on the PayHere Sandbox page.

The PayHere hash is generated on the server side using the Merchant Secret.

## 5. Files used

- `config/payhere.php` - PayHere Sandbox settings and hash generation
- `payment.php` - Creates the PayHere payment request
- `payment_notify.php` - Receives and verifies PayHere payment notifications
- `payment_return.php` - Shows the payment result
- `payment_cancel.php` - Handles cancelled payments
- `checkout.php` - Creates the pending order and payment record
- `database.sql` - Database structure and sample products
