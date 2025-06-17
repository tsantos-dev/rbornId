<?php

namespace App\Controllers;

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

    /**
     * Construtor da classe UserController.
     * Inicializa o modelo User.
     */
    public function __construct()
    {
        $this->userModel = new User();
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
                    // TODO: Implementar envio de email de confirmação (RF01)
                    echo "Usuário cadastrado com sucesso! ID: " . $userId . " (Implementar envio de e-mail de confirmação)";
                    // Redirecionar ou exibir mensagem de sucesso
                    // Em um sistema completo, você usaria um header('Location: ...');
                    return;
                } else {
                    $errors[] = "Erro ao cadastrar o usuário. Tente novamente.";
                }
            }

            // 3. Se houver erros, exibe-os no formulário
            // (No MVP, reutilizamos o form com mensagens de erro)
            require APP_PATH . '/Views/User/register.php';
        } else {
            // Se não for POST, redireciona para o formulário de registro
            header('Location: /user/register'); // Ajustar conforme sua rota
            exit;
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

    // TODO: Implementar métodos para RF02 (login, recuperação de senha, etc.)
}