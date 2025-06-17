<?php
/**
 * Ponto de entrada da aplicação R-Born Id.
 *
 * Este script inicializa a aplicação, carrega configurações,
 * e direciona as requisições para os controllers apropriados.
 */

// Define o PATH_ROOT como o diretório atual, pois public_html é a raiz do projeto.
define('PATH_ROOT', __DIR__);
define('APP_PATH', PATH_ROOT . '/app'); // Caminho para o diretório 'app'

// Autoload do Composer
require_once PATH_ROOT . '/vendor/autoload.php';

// Carregar variáveis de ambiente do .env
$dotenv = Dotenv\Dotenv::createImmutable(PATH_ROOT);
$dotenv->load();

// Opcional: Carregar configurações globais ou do banco de dados
// Ex: $dbConfig = require PATH_ROOT . '/config/database.php';
// if (class_exists('\App\Core\Database')) {
// \App\Core\Database::setConfig($dbConfig); // Se você tiver um método estático para isso
// }

// Inicializa o roteador
$router = new \App\Core\Router(); // Usando FQCN (Fully Qualified Class Name)

// --- Definição de Rotas ---

// Rota Principal
$router->addRoute('GET', '/', 'HomeController@index'); // Controller e método para a página inicial

// RF01: Cadastro de Usuário
$router->addRoute('GET', '/user/register', 'UserController@register'); // Exibe formulário de cadastro
$router->addRoute('POST', '/user/create', 'UserController@create');    // Processa cadastro
$router->addRoute('GET', '/user/verify/{token}', 'UserController@verifyEmail'); // Verifica e-mail

// RF02: Login de Usuário (exemplos de rotas)
// $router->addRoute('GET', '/login', 'UserController@loginForm');
// $router->addRoute('POST', '/login', 'UserController@login');
// $router->addRoute('GET', '/logout', 'UserController@logout');

// RF03 & RF05: Bebês (cadastro e visualização)
// $router->addRoute('GET', '/baby/new', 'BabyController@createForm'); // Formulário para RF03
// $router->addRoute('POST', '/baby/save', 'BabyController@save');     // Salvar dados do RF03
$router->addRoute('GET', '/baby/{registration_number}', 'BabyController@show'); // Exibir bebê (usado por RF05)

// RF04: Geração de Documentos (exemplo)
// $router->addRoute('GET', '/document/rg/{registration_number}', 'DocumentController@generateRg');

// RF06: API REST
$router->addRoute('GET', '/api/babies/{registration_number}', 'ApiController@getBaby');
$router->addRoute('GET', '/api/validate/{registration_number}', 'ApiController@validateRegistration');

// --- Fim da Definição de Rotas ---

// Processa a requisição
$url = $_GET['url'] ?? '/'; // A URL é passada pelo .htaccess como $_GET['url']
$router->dispatch($_SERVER['REQUEST_METHOD'], $url);