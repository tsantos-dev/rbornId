<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class BabyCinDocumentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Encontra os dados do documento CIN de um bebê pelo ID do bebê.
     *
     * @param int $babyId O ID do bebê.
     * @return array|false Retorna os dados do documento CIN ou false se não encontrado.
     */
    public function findByBabyId(int $babyId): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM baby_cin_documents WHERE baby_id = :baby_id");
        $stmt->bindParam(':baby_id', $babyId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria ou atualiza os dados do documento CIN para um bebê.
     *
     * @param int $babyId O ID do bebê.
     * @param array $data Dados a serem inseridos/atualizados.
     *        Campos esperados: social_name, blood_type, rh_factor, health_conditions,
     *                          place_of_birth_city, place_of_birth_state, nationality,
     *                          issue_date, expiry_date, cin_qr_code_data
     * @return int|false Retorna o ID do registro CIN (novo ou existente) em sucesso, false em falha.
     */
    public function createOrUpdateForBaby(int $babyId, array $data): int|false
    {
        $existingCin = $this->findByBabyId($babyId);

        if ($existingCin) {
            // Atualiza
            $sql = "UPDATE baby_cin_documents SET 
                        social_name = :social_name, 
                        blood_type = :blood_type, 
                        rh_factor = :rh_factor, 
                        health_conditions = :health_conditions,
                        place_of_birth_city = :place_of_birth_city,
                        place_of_birth_state = :place_of_birth_state,
                        nationality = :nationality,
                        issue_date = :issue_date,
                        expiry_date = :expiry_date,
                        cin_qr_code_data = :cin_qr_code_data,
                        updated_at = NOW()
                    WHERE baby_id = :baby_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':baby_id', $babyId, PDO::PARAM_INT);
        } else {
            // Insere
            $sql = "INSERT INTO baby_cin_documents (baby_id, social_name, blood_type, rh_factor, health_conditions, place_of_birth_city, place_of_birth_state, nationality, issue_date, expiry_date, cin_qr_code_data, created_at, updated_at)
                    VALUES (:baby_id, :social_name, :blood_type, :rh_factor, :health_conditions, :place_of_birth_city, :place_of_birth_state, :nationality, :issue_date, :expiry_date, :cin_qr_code_data, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':baby_id', $babyId, PDO::PARAM_INT);
        }

        try {
            $stmt->bindParam(':social_name', $data['social_name']);
            $stmt->bindParam(':blood_type', $data['blood_type']);
            $stmt->bindParam(':rh_factor', $data['rh_factor']);
            $stmt->bindParam(':health_conditions', $data['health_conditions']);
            $stmt->bindParam(':place_of_birth_city', $data['place_of_birth_city']);
            $stmt->bindParam(':place_of_birth_state', $data['place_of_birth_state']);
            $stmt->bindParam(':nationality', $data['nationality']);
            $stmt->bindParam(':issue_date', $data['issue_date']); // Pode ser NULL inicialmente
            $stmt->bindParam(':expiry_date', $data['expiry_date']); // Pode ser NULL inicialmente
            $stmt->bindParam(':cin_qr_code_data', $data['cin_qr_code_data']); // Pode ser NULL inicialmente

            if ($stmt->execute()) {
                return $existingCin ? (int)$existingCin['id'] : (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao salvar dados da CIN: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza as datas de emissão e validade de um documento CIN.
     *
     * @param int $babyCinDocumentId O ID do registro na tabela baby_cin_documents.
     * @param string|null $issueDate Data de emissão.
     * @param string|null $expiryDate Data de validade.
     * @return bool
     */
    public function updateDates(int $babyCinDocumentId, ?string $issueDate, ?string $expiryDate): bool
    {
        $stmt = $this->db->prepare("UPDATE baby_cin_documents SET issue_date = :issue_date, expiry_date = :expiry_date, updated_at = NOW() WHERE id = :id");
        $stmt->bindParam(':issue_date', $issueDate);
        $stmt->bindParam(':expiry_date', $expiryDate);
        $stmt->bindParam(':id', $babyCinDocumentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}