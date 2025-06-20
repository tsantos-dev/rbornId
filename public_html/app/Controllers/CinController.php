<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Baby;
use App\Models\BabyCinDocumentModel;

class CinController extends Controller
{
    private Baby $babyModel;
    private BabyCinDocumentModel $babyCinDocumentModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Protege todas as ações deste controller, exigindo que o usuário esteja logado.
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit;
        }

        $this->babyModel = new Baby();
        $this->babyCinDocumentModel = new BabyCinDocumentModel();
    }

    /**
     * Exibe o formulário para solicitar/editar os dados da CIN para um bebê.
     *
     * @param string $baby_registration_number O número de registro do bebê.
     * @return void
     */
    public function requestForm(string $baby_registration_number): void
    {
        $baby = $this->babyModel->findByRegistrationNumber($baby_registration_number);

        // Verifica se o bebê existe e pertence ao usuário logado
        if (!$baby || $baby['user_id'] !== $_SESSION['user_id']) {
            $this->view('Errors/404', ['message' => 'Bebê não encontrado ou você não tem permissão para acessá-lo.']);
            return;
        }

        $cinData = $this->babyCinDocumentModel->findByBabyId((int)$baby['id']);

        // Se não houver dados CIN existentes, inicializa com valores padrão
        if (!$cinData) {
            $cinData = [
                'social_name' => '',
                'blood_type' => '',
                'rh_factor' => '',
                'health_conditions' => '',
                'place_of_birth_city' => $baby['maternity'], // Pode usar a maternidade como cidade de nascimento padrão
                'place_of_birth_state' => '', // TODO: Definir um padrão ou deixar vazio
                'nationality' => 'Brasileira',
                'issue_date' => null,
                'expiry_date' => null,
                'cin_qr_code_data' => null,
            ];
        }

        $this->view('Cin/request_form', [
            'baby' => $baby,
            'cinData' => $cinData,
            'errors' => $_SESSION['errors'] ?? [], // Para exibir erros de validação após redirecionamento
            'post' => $_SESSION['post_data'] ?? [], // Para repopular o formulário
        ]);

        // Limpa os dados da sessão após exibi-los
        unset($_SESSION['errors']);
        unset($_SESSION['post_data']);
    }

    /**
     * Processa o envio do formulário de dados da CIN.
     *
     * @param string $baby_registration_number O número de registro do bebê.
     * @return void
     */
    public function processRequest(string $baby_registration_number): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cin/request/' . $baby_registration_number);
            exit;
        }

        $baby = $this->babyModel->findByRegistrationNumber($baby_registration_number);

        // Verifica se o bebê existe e pertence ao usuário logado
        if (!$baby || $baby['user_id'] !== $_SESSION['user_id']) {
            $this->view('Errors/404', ['message' => 'Bebê não encontrado ou você não tem permissão para acessá-lo.']);
            return;
        }

        // 1. Coletar e sanitizar os dados do POST
        $data = [
            'social_name' => trim(filter_input(INPUT_POST, 'social_name', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'place_of_birth_city' => trim(filter_input(INPUT_POST, 'place_of_birth_city', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'place_of_birth_state' => trim(filter_input(INPUT_POST, 'place_of_birth_state', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'nationality' => trim(filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'blood_type' => trim(filter_input(INPUT_POST, 'blood_type', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'rh_factor' => trim(filter_input(INPUT_POST, 'rh_factor', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            'health_conditions' => trim(filter_input(INPUT_POST, 'health_conditions', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
            // Campos que serão definidos após o pagamento
            'issue_date' => null,
            'expiry_date' => null,
            'cin_qr_code_data' => null,
        ];

        // 2. Validar os dados
        $errors = $this->validateCinData($data);

        if (!empty($errors)) {
            // Salvar erros e dados do post na sessão para repopular o formulário
            $_SESSION['errors'] = $errors;
            $_SESSION['post_data'] = $_POST;
            header('Location: /cin/request/' . $baby_registration_number);
            exit;
        }

        // 3. Salvar os dados no banco
        $cinId = $this->babyCinDocumentModel->createOrUpdateForBaby((int)$baby['id'], $data);

        if ($cinId) {
            // Sucesso! Agora, iniciar o fluxo de pagamento.
            // TODO: Implementar a criação da sessão de checkout do Stripe aqui.
            // Por enquanto, vamos redirecionar para o dashboard com uma mensagem de sucesso (simulando).
            $_SESSION['success_message'] = 'Dados da CIN salvos com sucesso! Próximo passo: pagamento.';
            header('Location: /dashboard');
            exit;
        } else {
            // Erro ao salvar no banco
            $_SESSION['errors'] = ['Ocorreu um erro ao salvar os dados. Por favor, tente novamente.'];
            $_SESSION['post_data'] = $_POST;
            header('Location: /cin/request/' . $baby_registration_number);
            exit;
        }
    }

    private function validateCinData(array $data): array
    {
        $errors = [];
        $validUfs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
        $validBloodTypes = ['A', 'B', 'AB', 'O', ''];
        $validRhFactors = ['Positivo', 'Negativo', ''];

        if (empty($data['place_of_birth_city'])) {
            $errors[] = 'Cidade de Nascimento é obrigatória.';
        }
        if (empty($data['place_of_birth_state'])) {
            $errors[] = 'Estado de Nascimento (UF) é obrigatório.';
        } elseif (!in_array($data['place_of_birth_state'], $validUfs)) {
            $errors[] = 'Estado de Nascimento (UF) inválido.';
        }
        if (empty($data['nationality'])) {
            $errors[] = 'Nacionalidade é obrigatória.';
        }
        if (!in_array($data['blood_type'], $validBloodTypes)) {
            $errors[] = 'Tipo Sanguíneo inválido.';
        }
        if (!in_array($data['rh_factor'], $validRhFactors)) {
            $errors[] = 'Fator RH inválido.';
        }
        // Validação cruzada: se um campo de sangue for preenchido, o outro também deve ser.
        if ((!empty($data['blood_type']) && empty($data['rh_factor'])) || (empty($data['blood_type']) && !empty($data['rh_factor']))) {
            $errors[] = 'Tipo Sanguíneo e Fator RH devem ser preenchidos em conjunto.';
        }

        return $errors;
    }
}