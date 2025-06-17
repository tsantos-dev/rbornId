<?php

namespace App\Core;

use PDO;
use PDOException;
use Exception;

/**
 * Class Database
 *
 * Gerencia a conexão com o banco de dados utilizando o padrão Singleton.
 * As configurações de conexão são carregadas do arquivo `config/database.php`.
 */
class Database
{
    /** @var PDO|null Instância única da conexão PDO. */
    private static ?PDO $instance = null;

    /** @var array|null Configurações do banco de dados. */
    private static ?array $config = null;

    /**
     * Construtor privado para prevenir a instanciação direta.
     */
    private function __construct()
    {
    }

    /**
     * Previne a clonagem da instância.
     */
    private function __clone()
    {
    }

    /**
     * Previne a desserialização da instância.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    /**
     * Obtém a instância da conexão PDO (Singleton).
     *
     * Carrega as configurações do banco de dados do arquivo `config/database.php`
     * na primeira chamada e estabelece a conexão.
     *
     * @return PDO Instância da conexão PDO.
     * @throws PDOException Se a conexão com o banco de dados falhar.
     * @throws Exception Se o arquivo de configuração do banco de dados não for encontrado ou estiver malformado.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $configPath = PATH_ROOT . '/config/database.php';
            if (!file_exists($configPath)) {
                throw new Exception("Arquivo de configuração do banco de dados não encontrado: {$configPath}");
            }

            self::$config = require $configPath;

            if (!is_array(self::$config) || empty(self::$config['dsn']) || empty(self::$config['username'])) {
                throw new Exception("Configuração do banco de dados inválida ou incompleta.");
            }

            $dsn = self::$config['dsn'];
            $username = self::$config['username'];
            $password = self::$config['password'] ?? null; // Senha pode ser opcional/nula
            $options = self::$config['options'] ?? [];

            self::$instance = new PDO($dsn, $username, $password, $options);
        }
        return self::$instance;
    }
}