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

        // TODO: Implementar validação dos dados do POST
        // TODO: Salvar/Atualizar dados na baby_cin_documents usando $this->babyCinDocumentModel->createOrUpdateForBaby()
        // TODO: Iniciar fluxo de pagamento (RF07)
        // TODO: Redirecionar para a página de sucesso/pagamento

        // Exemplo de redirecionamento temporário para o dashboard
        header('Location: /dashboard');
        exit;
    }
}