<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ApiKey
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Verifica se uma chave API é válida.
     *
     * @param string $key A chave API a ser validada.
     * @return bool True se a chave for válida, false caso contrário.
     */
    public function isValid(string $key): bool
    {
        $stmt = $this->db->prepare("SELECT id, user_id FROM api_keys WHERE api_key = :api_key LIMIT 1");
        $stmt->bindParam(':api_key', $key);
        $stmt->execute();
        $apiKeyData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($apiKeyData) {
            // Opcional: Atualizar last_used_at
            $this->updateLastUsedAt($apiKeyData['id']);
            return true;
        }
        return false;
    }

    /**
     * Atualiza o campo last_used_at para uma chave API.
     *
     * @param int $apiKeyId O ID da chave API.
     */
    private function updateLastUsedAt(int $apiKeyId): void
    {
        $stmt = $this->db->prepare("UPDATE api_keys SET last_used_at = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $apiKeyId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // TODO: Métodos para criar/gerenciar chaves API para usuários.
}