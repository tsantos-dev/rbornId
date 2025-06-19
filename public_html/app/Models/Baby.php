<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Class Baby
 *
 * Modelo para gerenciar dados de bebês reborn.
 * Interage com a tabela 'babies' no banco de dados.
 */
class Baby
{
    /** @var PDO Instância da conexão com o banco de dados. */
    private PDO $db;

    /**
     * Construtor da classe Baby.
     * Obtém a instância da conexão com o banco de dados.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cria um novo registro de bebê no banco de dados.
     *
     * @param array $data Dados do bebê. Espera-se chaves como:
     *  'user_id', 'registration_number', 'name', 'birth_date', 'gender',
     *  'weight', 'height', 'mother_name', 'father_name', 'maternity',
     *  'civil_registration', 'characteristics', 'image_path'.
     * @return int|false Retorna o ID do bebê inserido em caso de sucesso, ou false em caso de falha.
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO babies (user_id, registration_number, name, birth_date, gender, 
                                  weight, height, mother_name, father_name, maternity, 
                                  civil_registration, characteristics, image_path, 
                                  created_at, updated_at) 
                VALUES (:user_id, :registration_number, :name, :birth_date, :gender, 
                        :weight, :height, :mother_name, :father_name, :maternity, 
                        :civil_registration, :characteristics, :image_path, 
                        NOW(), NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':registration_number', $data['registration_number']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->bindParam(':gender', $data['gender']);
            $stmt->bindParam(':weight', $data['weight']);
            $stmt->bindParam(':height', $data['height']);
            $stmt->bindParam(':mother_name', $data['mother_name']);
            $stmt->bindParam(':father_name', $data['father_name']);
            $stmt->bindParam(':maternity', $data['maternity']);
            $stmt->bindParam(':civil_registration', $data['civil_registration']);
            $stmt->bindParam(':characteristics', $data['characteristics']);
            $stmt->bindParam(':image_path', $data['image_path']);

            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // Em produção, logar o erro.
            // error_log("Erro ao criar bebê: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Encontra um bebê pelo seu número de registro.
     *
     * @param string $registrationNumber O número de registro do bebê.
     * @return array|false Retorna os dados do bebê como array ou false se não encontrado.
     */
    public function findByRegistrationNumber(string $registrationNumber): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM babies WHERE registration_number = :registration_number");
        $stmt->bindParam(':registration_number', $registrationNumber);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Encontra um bebê pelo seu ID.
     *
     * @param int $id O ID do bebê.
     * @return array|false Retorna os dados do bebê como array ou false se não encontrado.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM babies WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Encontra um bebê pelo seu número de registro civil.
     *
     * @param string $civilRegistration O número de registro civil do bebê.
     * @return array|false Retorna os dados do bebê como array ou false se não encontrado.
     */
    public function findByCivilRegistration(string $civilRegistration): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM babies WHERE civil_registration = :civil_registration");
        $stmt->bindParam(':civil_registration', $civilRegistration);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Encontra todos os bebês pertencentes a um usuário específico.
     *
     * @param int $userId O ID do usuário.
     * @return array Retorna um array de bebês.
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT id, name, registration_number, image_path FROM babies WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Atualiza o campo civil_registration de um bebê.
     *
     * @param int $babyId O ID do bebê.
     * @param string $civilRegistration O novo número de registro civil.
     * @return bool Retorna true em caso de sucesso, false caso contrário.
     */
    public function updateCivilRegistration(int $babyId, string $civilRegistration): bool
    {
        $sql = "UPDATE babies SET civil_registration = :civil_registration WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':civil_registration', $civilRegistration);
            $stmt->bindParam(':id', $babyId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // error_log("Erro ao atualizar civil_registration: " . $e->getMessage());
            return false;
        }
    }
    // TODO: Implementar métodos update() e delete() se necessário.
}