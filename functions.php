<?php
require_once 'config.php';

function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

function validateAddress($address, $currency = CURRENCY) {
    $patterns = [
        'BTC' => '/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$|^(bc1)[0-9A-Za-z]{39,59}$/',
        'LTC' => '/^[LM3][a-km-zA-HJ-NP-Z1-9]{26,33}$/',
        'DOGE' => '/^D{1}[5-9A-HJ-NP-U]{1}[1-9A-HJ-NP-Za-km-z]{32}$/',
        'ETH' => '/^0x[a-fA-F0-9]{40}$/',
        'TRX' => '/^T[a-zA-Z0-9]{33}$/'
    ];
    
    if (isset($patterns[$currency])) {
        return preg_match($patterns[$currency], $address);
    }
    
    return false;
}

function formatAmount($amount) {
    return number_format($amount, 8, '.', '');
}

function showMessage($message, $type = 'info') {
    $alertClass = $type === 'error' ? 'danger' : $type;
    return "<div class='alert alert-$alertClass alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

function timeRemaining($lastClaimTime) {
    $now = time();
    $lastClaim = strtotime($lastClaimTime);
    $nextClaim = $lastClaim + CLAIM_INTERVAL;
    $remaining = $nextClaim - $now;
    
    if ($remaining <= 0) {
        return 0;
    }
    
    $hours = floor($remaining / 3600);
    $minutes = floor(($remaining % 3600) / 60);
    $seconds = $remaining % 60;
    
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

function generateCaptcha() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $operation = rand(0, 1) ? '+' : '-';
    
    if ($operation == '+') {
        $answer = $num1 + $num2;
    } else {
        $answer = $num1 - $num2;
    }
    
    $_SESSION['captcha_answer'] = $answer;
    return "$num1 $operation $num2";
}

function verifyCaptcha($userAnswer) {
    return isset($_SESSION['captcha_answer']) && $_SESSION['captcha_answer'] == $userAnswer;
}
?>
