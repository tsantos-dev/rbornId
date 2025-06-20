<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class PaymentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cria um novo registro de pagamento pendente.
     *
     * @param int $userId
     * @param int $babyId
     * @param string $documentType
     * @param float $amount
     * @param string $currency
     * @param string $stripeSessionId
     * @return int|false O ID do pagamento criado ou false em caso de falha.
     */
    public function create(int $userId, int $babyId, string $documentType, float $amount, string $currency, string $stripeSessionId): int|false
    {
        $sql = "INSERT INTO payments (user_id, baby_id, document_type, amount, currency, status, stripe_session_id, created_at, updated_at)
                VALUES (:user_id, :baby_id, :document_type, :amount, :currency, 'pending', :stripe_session_id, NOW(), NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':baby_id', $babyId, PDO::PARAM_INT);
            $stmt->bindParam(':document_type', $documentType);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':currency', $currency);
            $stmt->bindParam(':stripe_session_id', $stripeSessionId);

            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao criar registro de pagamento: " . $e->getMessage());
            return false;
        }
    }

    // TODO: Adicionar métodos para encontrar e atualizar pagamentos, que serão usados pelo webhook.
    // public function findBySessionId(string $sessionId): array|false { ... }
    // public function updateStatusBySessionId(string $sessionId, string $status, ?string $paymentIntentId): bool { ... }
}