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

    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_DURATION_MINUTES = 15; // Duração do bloqueio em minutos

    /**
     * Construtor da classe UserController.
     * Inicializa o modelo User.
     */
    public function __construct()
    {
        // parent::__construct(); // Removido pois App\Core\Controller não tem construtor
        // Iniciar a sessão se ainda não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
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
        $this->view('User/register');
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

                    // if ($this->userModel->setEmailVerificationToken($userId, $token, $expiresAt)) {
                    //     $verificationLink = $this->getBaseUrl() . "/user/verify/{$token}";
                    //     $emailBody = "<h1>Bem-vindo ao R-Born Id!</h1>
                    //                   <p>Obrigado por se cadastrar. Por favor, clique no link abaixo para verificar seu e-mail:</p>
                    //                   <p><a href='{$verificationLink}'>Verificar E-mail</a></p>
                    //                   <p>Se você não se cadastrou, por favor ignore este e-mail.</p>";
                        
                    //     if ($this->mailService->send($email, "Confirme seu E-mail - R-Born Id", $emailBody)) {
                    //         // Exibir mensagem de sucesso com instrução para verificar e-mail
                    //         $this->view('User/registration_success', ['email' => $email]);
                    //         return;
                    //     }
                    //     $errors[] = "Usuário cadastrado, mas houve um erro ao enviar o e-mail de confirmação. Por favor, contate o suporte.";
                    // } else {
                    //     $errors[] = "Erro ao preparar a verificação de e-mail. Por favor, contate o suporte.";
                    // }
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
     * Realiza o logout do usuário.
     * Destrói a sessão e redireciona para a página de login.
     *
     * @return void
     */
    public function logout(): void
    {
        // Destruir todas as variáveis de sessão.
        $_SESSION = [];

        // Se é desejável destruir a sessão completamente, apague também o cookie de sessão.
        // Nota: Isso destruirá a sessão, e não apenas os dados da sessão!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalmente, destruir a sessão.
        session_destroy();

        // Redirecionar para a página de login
        header('Location: /user/login');
        exit;
    }
    /**
     * Exibe o formulário de login.
     *
     * @return void
     */
    public function loginForm(): void
    {
        $this->view('User/login');
    }

    /**
     * Processa a autenticação do usuário.
     *
     * @return void
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $errors = [];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "E-mail inválido.";
            }
            if (empty($password)) {
                $errors[] = "Senha é obrigatória.";
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if ($user) {
                    // Verificar se a conta está bloqueada
                    if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                        $remainingTime = strtotime($user['account_locked_until']) - time();
                        $minutes = ceil($remainingTime / 60);
                        $errors[] = "Sua conta está temporariamente bloqueada. Tente novamente em {$minutes} minuto(s).";
                        $this->view('User/login', ['errors' => $errors, 'post' => $_POST]);
                        return;
                    }

                    // Verificar se o e-mail foi confirmado
                    // if (empty($user['email_verified_at'])) {
                    //     $errors[] = "Por favor, confirme seu e-mail antes de fazer login.";
                    //     $this->view('User/login', ['errors' => $errors, 'post' => $_POST]);
                    //     return;
                    // }

                    // Se a conta estava bloqueada mas o tempo expirou, resetar tentativas antes de verificar senha
                    // Isso garante que o usuário tenha novas chances após o período de bloqueio.
                    if ($user['account_locked_until'] && strtotime($user['account_locked_until']) <= time()) {
                        if ($user['failed_login_attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
                             $this->userModel->resetFailedLoginAttempts((int)$user['id']);
                             $user['failed_login_attempts'] = 0; // Atualiza a variável local para a lógica subsequente
                        }
                    }

                    if (password_verify($password, $user['password'])) {
                        // Login bem-sucedido
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        // TODO: Limpar tentativas de login falhas para este usuário
                        // TODO: Redirecionar para o painel do usuário ou página inicial logada
                        $this->userModel->resetFailedLoginAttempts((int)$user['id']);
                        header('Location: /dashboard'); 
                        exit;
                    }
                }
                // Se chegou aqui, o e-mail não existe ou a senha está incorreta
                // Incrementar tentativas de login falhas se o usuário existir
                if ($user) {
                    $this->userModel->incrementFailedLoginAttempts($email);
                    $currentAttempts = ($user['failed_login_attempts'] ?? 0) + 1; // Pega o valor atualizado ou assume 1
                    if ($currentAttempts >= self::MAX_LOGIN_ATTEMPTS) {
                        $this->userModel->lockAccount($email, self::LOCKOUT_DURATION_MINUTES);
                        $errors[] = "Sua conta foi temporariamente bloqueada devido a múltiplas tentativas de login malsucedidas. Tente novamente em " . self::LOCKOUT_DURATION_MINUTES . " minutos.";
                        $this->view('User/login', ['errors' => $errors, 'post' => $_POST]);
                        return;
                    }
                }
                $errors[] = "E-mail ou senha inválidos.";
            }

            $this->view('User/login', ['errors' => $errors, 'post' => $_POST]);

        } else {
            header('Location: /user/login'); // Rota para o formulário de login
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

    /**
     * Exibe a página de perfil do usuário.
     *
     * @return void
     */
    public function profile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId); // findById precisa existir no UserModel

        if (!$user) {
            // Isso não deveria acontecer se o user_id na sessão for válido
            // Mas é uma boa prática verificar.
            $this->view('Errors/404', ['message' => 'Usuário não encontrado.']);
            return;
        }
        $this->view('User/profile', ['user' => $user]);
    }

    /**
     * Exibe o formulário para solicitar a redefinição de senha.
     *
     * @return void
     */
    public function forgotPasswordForm(): void
    {
        $this->view('User/forgot_password_form');
    }

    /**
     * Processa a solicitação de redefinição de senha.
     * Gera um token, salva no banco e (eventualmente) envia um e-mail.
     *
     * @return void
     */
    public function sendPasswordResetLink(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $errors = [];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Formato de e-mail inválido.";
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expira em 1 hora

                    if ($this->userModel->setPasswordResetToken((int)$user['id'], $token, $expiresAt)) {
                        // TODO: Descomentar e adaptar o envio de e-mail quando o MailService estiver configurado
                        // $resetLink = $this->getBaseUrl() . "/user/reset-password/{$token}";
                        // $emailBody = "<h1>Redefinição de Senha</h1>
                        //               <p>Você solicitou a redefinição de senha para sua conta no R-Born Id.</p>
                        //               <p>Clique no link abaixo para criar uma nova senha:</p>
                        //               <p><a href='{$resetLink}'>Redefinir Senha</a></p>
                        //               <p>Este link expirará em 1 hora.</p>
                        //               <p>Se você não solicitou esta alteração, por favor ignore este e-mail.</p>";
                        
                        // if ($this->mailService->send($email, "Redefinição de Senha - R-Born Id", $emailBody)) {
                        //     $this->view('User/password_reset_sent', ['email' => $email]);
                        //     return;
                        // }
                        // $errors[] = "Não foi possível enviar o e-mail de redefinição. Tente novamente mais tarde.";
                        
                        // Simulação de envio de e-mail para desenvolvimento:
                        $_SESSION['password_reset_token_for_dev'] = $token; // Apenas para facilitar o teste local
                        $this->view('User/password_reset_sent', ['email' => $email, 'dev_token' => $token]);
                        return;
                    }
                    $errors[] = "Erro ao processar sua solicitação. Tente novamente.";
                } else {
                    // Não informar se o e-mail existe ou não por segurança, mas exibir mensagem genérica.
                    // No entanto, para o fluxo de teste, podemos ser mais diretos.
                    // $errors[] = "Se um e-mail correspondente for encontrado, um link de redefinição será enviado.";
                    // Para teste, vamos exibir a página de "enviado" mesmo que o email não exista,
                    // para não revelar quais emails estão cadastrados.
                    // A lógica real de envio só ocorreria se $user fosse encontrado.
                    $this->view('User/password_reset_sent', ['email' => $email]);
                    return;
                }
            }
            $this->view('User/forgot_password_form', ['errors' => $errors, 'post' => $_POST]);
        } else {
            header('Location: /user/forgot-password');
            exit;
        }
    }

    /**
     * Exibe o formulário para o usuário inserir a nova senha.
     *
     * @param string $token O token de redefinição.
     * @return void
     */
    public function resetPasswordForm(string $token): void
    {
        $user = $this->userModel->findByPasswordResetToken($token);
        if ($user) {
            $this->view('User/reset_password_form', ['token' => $token]);
        } else {
            $this->view('User/password_reset_failed', ['message' => 'Token de redefinição inválido ou expirado.']);
        }
    }

    /**
     * Processa a atualização da senha.
     *
     * @return void
     */
    public function updatePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $errors = [];

            $user = $this->userModel->findByPasswordResetToken($token ?? '');

            if (!$user) {
                $this->view('User/password_reset_failed', ['message' => 'Token de redefinição inválido ou expirado.']);
                return;
            }

            // Reutilizar a validação de senha do cadastro, se aplicável, ou criar uma específica.
            if (strlen($password) < 8) {
                $errors[] = "A senha deve ter no mínimo 8 caracteres.";
            } elseif (!preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9\s]/', $password)) {
                $errors[] = "A senha deve conter letras, números e caracteres especiais.";
            }
            if ($password !== $confirmPassword) {
                $errors[] = "As senhas não coincidem.";
            }

            if (empty($errors)) {
                if ($this->userModel->updatePassword((int)$user['id'], $password)) {
                    unset($_SESSION['password_reset_token_for_dev']); // Limpar token de dev
                    $this->view('User/password_reset_success');
                    return;
                }
                $errors[] = "Erro ao atualizar a senha. Tente novamente.";
            }
            $this->view('User/reset_password_form', ['errors' => $errors, 'token' => $token, 'post' => $_POST]);
        } else {
            header('Location: /user/login'); // Ou para uma página de erro genérica
            exit;
        }
    }

    // TODO: Implementar métodos para RF02 (login, recuperação de senha, etc.)
}