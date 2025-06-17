<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Class User
 *
 * Modelo para gerenciar dados de usuários.
 * Interage com a tabela 'users' no banco de dados.
 */
class User
{
    /** @var PDO Instância da conexão com o banco de dados. */
    private PDO $db;

    /**
     * Construtor da classe User.
     * Obtém a instância da conexão com o banco de dados.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cria um novo usuário no banco de dados.
     *
     * @param string $name Nome do usuário.
     * @param string $email E-mail do usuário.
     * @param string $cpf CPF do usuário.
     * @param string $password Senha do usuário (plain text, será hasheada).
     * @return int|false Retorna o ID do usuário inserido em caso de sucesso, ou false em caso de falha.
     */
    public function create(string $name, string $email, string $cpf, string $password): int|false
    {
        // Hashear a senha antes de salvar (RNF02)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (name, email, cpf, password, created_at, updated_at) 
                VALUES (:name, :email, :cpf, :password, NOW(), NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':cpf', $cpf); // Armazenar CPF com ou sem formatação, consistência é chave.
            $stmt->bindParam(':password', $hashedPassword);
            
            if ($stmt->execute()) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // Logar o erro em um ambiente de produção
            // error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Encontra um usuário pelo e-mail.
     *
     * @param string $email O e-mail do usuário a ser encontrado.
     * @return array|false Retorna os dados do usuário como array ou false se não encontrado.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT id, name, email, cpf, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Encontra um usuário pelo CPF.
     *
     * @param string $cpf O CPF do usuário a ser encontrado.
     * @return array|false Retorna os dados do usuário como array ou false se não encontrado.
     */
    public function findByCpf(string $cpf): array|false
    {
        // Considerar normalizar o CPF (remover pontos/traços) antes de buscar,
        // dependendo de como ele é armazenado.
        $stmt = $this->db->prepare("SELECT id, name, email, cpf, password FROM users WHERE cpf = :cpf");
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * TODO: Implementar validação de formato de CPF (RF01).
     * Esta função pode ser mais complexa, envolvendo cálculo de dígitos verificadores.
     * Por ora, uma validação simples de formato.
     *
     * @param string $cpf
     * @return bool
     */
    public function isValidCpfFormat(string $cpf): bool
    {
        // Exemplo: valida se tem o formato XXX.XXX.XXX-XX
        return (bool) preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf);
    }
}