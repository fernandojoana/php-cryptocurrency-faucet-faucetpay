<?php
// FaucetPay Configuration
define('FAUCETPAY_API_KEY', 'your_faucetpay_api_key_here');
define('FAUCETPAY_API_URL', 'https://faucetpay.io/api/v1');
define('CURRENCY', 'BTC'); // Change to your preferred currency

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'faucet_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Faucet Settings
define('CLAIM_AMOUNT', 0.00000010); // Amount to pay per claim
define('CLAIM_INTERVAL', 3600); // 1 hour between claims
define('MIN_PAYOUT', 0.00000050); // Minimum balance for auto payout

// Security Settings
define('CAPTCHA_SITE_KEY', 'your_captcha_site_key');
define('CAPTCHA_SECRET_KEY', 'your_captcha_secret_key');
?>
