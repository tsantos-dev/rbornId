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
// Se o usuário estiver logado, redireciona para o dashboard, senão para o HomeController
// Esta lógica pode ser colocada no HomeController@index ou aqui.
// Por simplicidade, vamos manter o HomeController@index para a página pública inicial.
$router->addRoute('GET', '/', 'HomeController@index'); // Controller e método para a página inicial

// RF01: Cadastro de Usuário
$router->addRoute('GET', '/user/register', 'UserController@register'); // Exibe formulário de cadastro
$router->addRoute('POST', '/user/create', 'UserController@create');    // Processa cadastro
$router->addRoute('GET', '/user/verify/{token}', 'UserController@verifyEmail'); // Verifica e-mail

// RF02: Login de Usuário (exemplos de rotas)
$router->addRoute('GET', '/user/login', 'UserController@loginForm');
$router->addRoute('POST', '/user/authenticate', 'UserController@authenticate');
$router->addRoute('GET', '/user/logout', 'UserController@logout');
$router->addRoute('GET', '/user/forgot-password', 'UserController@forgotPasswordForm');      // Exibe formulário para pedir reset
$router->addRoute('POST', '/user/send-reset-link', 'UserController@sendPasswordResetLink'); // Processa pedido e envia link
$router->addRoute('GET', '/user/reset-password/{token}', 'UserController@resetPasswordForm'); // Exibe formulário para nova senha
$router->addRoute('POST', '/user/update-password', 'UserController@updatePassword');         // Processa nova senha
$router->addRoute('GET', '/user/profile', 'UserController@profile'); // Rota para o perfil do usuário

// Dashboard do Usuário
$router->addRoute('GET', '/dashboard', 'DashboardController@index');

// RF03: Cadastro de Bebê Reborn & RF05: Visualização
$router->addRoute('GET', '/baby/new', 'BabyController@createForm'); // Exibe formulário de cadastro (RF03)
$router->addRoute('POST', '/baby/save', 'BabyController@save');     // Processa cadastro (RF03)
$router->addRoute('GET', '/baby/{registration_number}', 'BabyController@show'); // Exibir bebê (usado por RF05)

// RF04: Geração de Documentos (exemplo)
$router->addRoute('GET', '/document/birth-certificate/{registration_number}', 'DocumentController@generateBirthCertificatePdf');

// RF04: Geração de CIN (Carteira de Identidade Nacional)
$router->addRoute('GET', '/cin/request/{baby_registration_number}', 'CinController@requestForm'); // Exibe formulário de dados da CIN
$router->addRoute('POST', '/cin/process/{baby_registration_number}', 'CinController@processRequest'); // Processa dados e inicia pagamento
$router->addRoute('GET', '/cin/success', 'CinController@success'); // Página de sucesso do pagamento
$router->addRoute('GET', '/cin/cancel', 'CinController@cancel');   // Página de cancelamento do pagamento


// RF06: API REST
$router->addRoute('GET', '/api/babies/{civil_registration}', 'ApiController@getBaby');
$router->addRoute('GET', '/api/validate/{civil_registration}', 'ApiController@validateRegistration');

// Webhook do Stripe
$router->addRoute('POST', '/stripe/webhook', 'StripeWebhookController@handle');

// --- Fim da Definição de Rotas ---

// Processa a requisição
$url = $_GET['url'] ?? '/'; // A URL é passada pelo .htaccess como $_GET['url']
$router->dispatch($_SERVER['REQUEST_METHOD'], $url);