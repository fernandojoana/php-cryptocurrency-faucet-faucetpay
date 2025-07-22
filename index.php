<?php
session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'faucetpay.php';
require_once 'functions.php';

$db = new Database();
$faucetPay = new FaucetPayAPI();

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $captcha = $_POST['captcha'] ?? '';
    
    // Validate CAPTCHA
    if (!verifyCaptcha($captcha)) {
        $message = 'Resposta do CAPTCHA incorreta!';
        $messageType = 'error';
    } elseif (empty($address)) {
        $message = 'Por favor, insira um endereço válido!';
        $messageType = 'error';
    } elseif (!validateAddress($address)) {
        $message = 'Endereço inválido para ' . CURRENCY . '!';
        $messageType = 'error';
    } else {
        $ip = getClientIP();
        
        // Check rate limiting
        if (!$db->canClaim($address, $ip)) {
            $message = 'Você já reivindicou recentemente. Por favor, aguarde antes de tentar novamente.';
            $messageType = 'error';
        } else {
            // Check faucet balance
            $balance = $faucetPay->checkBalance();
            if ($balance['status'] != 200 || $balance['balance'] < CLAIM_AMOUNT) {
                $message = 'Faucet temporariamente sem fundos. Por favor, tente novamente mais tarde.';
                $messageType = 'error';
            } else {
                // Send payment
                $result = $faucetPay->sendPayment($address, CLAIM_AMOUNT);
                
                if ($result['status'] == 200) {
                    // Record claim
                    $db->recordClaim($address, $ip, CLAIM_AMOUNT);
                    $message = 'Pagamento enviado com sucesso! Valor: ' . formatAmount(CLAIM_AMOUNT) . ' ' . CURRENCY;
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao enviar pagamento: ' . ($result['message'] ?? 'Erro desconhecido');
                    $messageType = 'error';
                }
            }
        }
    }
}

// Get faucet balance
$balance = $faucetPay->checkBalance();
$faucetBalance = $balance['status'] == 200 ? $balance['balance'] : 0;

// Generate new CAPTCHA
$captchaQuestion = generateCaptcha();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo CURRENCY; ?> Faucet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-coins"></i> <?php echo CURRENCY; ?> Faucet
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-hand-holding-usd"></i> Reivindique <?php echo CURRENCY; ?> Grátis
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <?php echo showMessage($message, $messageType); ?>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <strong>Saldo do Faucet:</strong> 
                            <?php echo formatAmount($faucetBalance); ?> <?php echo CURRENCY; ?>
                        </div>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-wallet"></i> Seu Endereço <?php echo CURRENCY; ?>:
                                </label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="Insira seu endereço <?php echo CURRENCY; ?>" required>
                                <div class="form-text">Certifique-se de inserir um endereço válido.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="captcha" class="form-label">
                                    <i class="fas fa-shield-alt"></i> Resolva: <?php echo $captchaQuestion; ?> = ?
                                </label>
                                <input type="number" class="form-control" id="captcha" name="captcha" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-gift"></i> Reivindicar <?php echo formatAmount(CLAIM_AMOUNT); ?> <?php echo CURRENCY; ?>
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Você pode reivindicar a cada <?php echo CLAIM_INTERVAL/3600; ?> horas
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history"></i> Estatísticas
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $address = $_POST['address'] ?? '';
                        if ($address && validateAddress($address)) {
                            $totalPaid = $db->getTotalPaid($address);
                            echo "<p><strong>Total recebido por este endereço:</strong> " . 
                                 formatAmount($totalPaid) . " " . CURRENCY . "</p>";
                        }
                        ?>
                        <p class="text-muted">
                            <small>Faucet protegido por sistema anti-bot</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center py-4 mt-5">
        <div class="container">
            <p class="text-muted mb-0">
                <i class="fas fa-lock"></i> Pagamentos processados via FaucetPay
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
