<?php
/**
 * Configurações do Banco de Dados para R-Born Id.
 */
return [
    /**
     * Data Source Name (DSN) para conexão PDO.
     * Exemplo para MySQL: 'mysql:host=localhost;dbname=rborndb;charset=utf8mb4'
     * Exemplo para PostgreSQL: 'pgsql:host=localhost;port=5432;dbname=rborndb;user=dbuser;password=dbpass'
     */
    'dsn' => 'mysql:host=localhost;dbname=rborndb;charset=utf8mb4',

    'username' => 'root', // Seu usuário do banco de dados
    'password' => '',    // Sua senha do banco de dados

    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays associativos por padrão
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Desabilita emulação de prepared statements para segurança
    ],
];