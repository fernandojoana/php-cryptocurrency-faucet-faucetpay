<?php
class Database {
    private $connection;
    
    public function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $this->createTables();
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        $sql = "CREATE TABLE IF NOT EXISTS claims (
            id INT AUTO_INCREMENT PRIMARY KEY,
            address VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            amount DECIMAL(20,8) NOT NULL,
            claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_address (address),
            INDEX idx_ip (ip_address),
            INDEX idx_claimed_at (claimed_at)
        )";
        
        $this->connection->exec($sql);
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function canClaim($address, $ip) {
        $stmt = $this->connection->prepare("
            SELECT COUNT(*) FROM claims 
            WHERE (address = :address OR ip_address = :ip) 
            AND claimed_at > DATE_SUB(NOW(), INTERVAL :interval SECOND)
        ");
        $stmt->execute([
            ':address' => $address,
            ':ip' => $ip,
            ':interval' => CLAIM_INTERVAL
        ]);
        return $stmt->fetchColumn() == 0;
    }
    
    public function recordClaim($address, $ip, $amount) {
        $stmt = $this->connection->prepare("
            INSERT INTO claims (address, ip_address, amount) 
            VALUES (:address, :ip, :amount)
        ");
        return $stmt->execute([
            ':address' => $address,
            ':ip' => $ip,
            ':amount' => $amount
        ]);
    }
    
    public function getTotalPaid($address) {
        $stmt = $this->connection->prepare("
            SELECT SUM(amount) FROM claims WHERE address = :address
        ");
        $stmt->execute([':address' => $address]);
        return $stmt->fetchColumn() ?: 0;
    }
}
?>
