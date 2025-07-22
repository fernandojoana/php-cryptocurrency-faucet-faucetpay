<?php
require_once 'config.php';

class FaucetPayAPI {
    private $apiKey;
    private $apiUrl;
    
    public function __construct() {
        $this->apiKey = FAUCETPAY_API_KEY;
        $this->apiUrl = FAUCETPAY_API_URL;
    }
    
    public function sendPayment($to, $amount, $currency = CURRENCY) {
        $data = [
            'api_key' => $this->apiKey,
            'to' => $to,
            'amount' => $amount,
            'currency' => $currency
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            return $result;
        }
        
        return ['status' => 500, 'message' => 'API request failed'];
    }
    
    public function checkBalance($currency = CURRENCY) {
        $data = [
            'api_key' => $this->apiKey,
            'currency' => $currency
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/balance');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result;
    }
    
    public function validateAddress($address, $currency = CURRENCY) {
        $data = [
            'api_key' => $this->apiKey,
            'address' => $address,
            'currency' => $currency
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/checkaddress');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return isset($result['status']) && $result['status'] == 200;
    }
}
?>
