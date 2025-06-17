<?php

namespace App\Controllers;

use App\Core\MailService;
use App\Models\User;
use App\Core\Controller;

/**
 * Class UserController
 *
 * Controller para gerenciar ações relacionadas a usuários, como cadastro e login.
 */
class UserController extends Controller
{
    /** @var User Instância do modelo User. */
    private User $userModel;
    /** @var MailService Instância do serviço de e-mail. */
    private MailService $mailService;

    /**
     * Construtor da classe UserController.
     * Inicializa o modelo User.
     */
    public function __construct()
    {
        parent::__construct(); // Se o Controller base tiver um construtor
        $this->userModel = new User();
        $this->mailService = new MailService();
    }

    /**
     * Exibe o formulário de cadastro de usuário.
     *
     * @return void
     */
    public function register(): void
    {
        // No MVP, vamos simplesmente incluir a view.
        // Em um sistema mais completo, usaríamos um mecanismo de template.
        require APP_PATH . '/Views/User/register.php';
    }

    /**
     * Processa o cadastro de um novo usuário.
     *
     * @return void
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Sanitize e valida os dados de entrada
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = $_POST['password'] ?? ''; // Senhas não são "sanitizadas" com filter_var
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = $this->validateRegistrationData($name, $email, $cpf, $password, $confirmPassword);

            if (empty($errors)) {
                // 2. Tenta criar o usuário
                $userId = $this->userModel->create($name, $email, $cpf, $password);

                if ($userId) {
                    // 3. Gerar e enviar e-mail de confirmação
                    $token = bin2hex(random_bytes(32)); // Gera um token seguro
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expira em 1 hora

                    if ($this->userModel->setEmailVerificationToken($userId, $token, $expiresAt)) {
                        $verificationLink = $this->getBaseUrl() . "/user/verify/{$token}";
                        $emailBody = "<h1>Bem-vindo ao R-Born Id!</h1>
                                      <p>Obrigado por se cadastrar. Por favor, clique no link abaixo para verificar seu e-mail:</p>
                                      <p><a href='{$verificationLink}'>Verificar E-mail</a></p>
                                      <p>Se você não se cadastrou, por favor ignore este e-mail.</p>";
                        
                        if ($this->mailService->send($email, "Confirme seu E-mail - R-Born Id", $emailBody)) {
                            // Exibir mensagem de sucesso com instrução para verificar e-mail
                            $this->view('User/registration_success', ['email' => $email]);
                            return;
                        }
                        $errors[] = "Usuário cadastrado, mas houve um erro ao enviar o e-mail de confirmação. Por favor, contate o suporte.";
                    } else {
                        $errors[] = "Erro ao preparar a verificação de e-mail. Por favor, contate o suporte.";
                    }
                    return;
                } else {
                    $errors[] = "Erro ao cadastrar o usuário. Tente novamente.";
                }
            }

            // 3. Se houver erros, exibe-os no formulário
            // (No MVP, reutilizamos o form com mensagens de erro)
            $this->view('User/register', ['errors' => $errors, 'post' => $_POST]);
        } else {
            // Se não for POST, redireciona para o formulário de registro
            header('Location: /user/register'); // Ajustar conforme sua rota
            exit;
        }
    }

    /**
     * Processa a verificação do e-mail do usuário.
     *
     * @param string $token O token de verificação.
     * @return void
     */
    public function verifyEmail(string $token): void
    {
        $user = $this->userModel->findByVerificationToken($token);

        if ($user) {
            if ($this->userModel->verifyEmail((int)$user['id'])) {
                // E-mail verificado com sucesso
                // TODO: Redirecionar para login ou página de sucesso
                $this->view('User/verification_success');
            } else {
                // Erro ao atualizar o status de verificação
                // TODO: Exibir mensagem de erro mais específica
                $this->view('User/verification_failed', ['message' => 'Ocorreu um erro ao verificar seu e-mail. Tente novamente ou contate o suporte.']);
            }
        } else {
            // Token inválido ou expirado
            $this->view('User/verification_failed', ['message' => 'Link de verificação inválido ou expirado.']);
        }
    }





    /**
     * Valida os dados do formulário de registro.
     *
     * @param string $name
     * @param string $email
     * @param string $cpf
     * @param string $password
     * @param string $confirmPassword
     * @return array Array de erros, se houver.
     */
    private function validateRegistrationData(string $name, string $email, string $cpf, string $password, string $confirmPassword): array
    {
        $errors = [];

        if (empty($name)) {
            $errors[] = "O nome é obrigatório.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "E-mail inválido.";
        } elseif ($this->userModel->findByEmail($email)) {
            $errors[] = "Este e-mail já está cadastrado.";
        }

        if (empty($cpf)) {
            $errors[] = "CPF é obrigatório.";
        } elseif (!$this->userModel->isValidCpfFormat($cpf)) {
            $errors[] = "CPF inválido. Formato esperado: XXX.XXX.XXX-XX";
        } elseif ($this->userModel->findByCpf($cpf)) {
            $errors[] = "Este CPF já está cadastrado.";
        }

        if (strlen($password) < 8) {
            $errors[] = "A senha deve ter no mínimo 8 caracteres.";
        } elseif (!preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9\s]/', $password)) {
            // TODO: melhorar a validação de força da senha (RNF02)
            $errors[] = "A senha deve conter letras, números e caracteres especiais.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "As senhas não coincidem.";
        }

        return $errors;
    }

    /**
     * Obtém a URL base da aplicação.
     * Necessário para construir links absolutos nos e-mails.
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }



    // TODO: Implementar métodos para RF02 (login, recuperação de senha, etc.)
}