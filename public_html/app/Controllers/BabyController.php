<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Baby;
use Ramsey\Uuid\Uuid; // Para gerar registration_number único

/**
 * Class BabyController
 *
 * Controller para gerenciar ações relacionadas a bebês reborn.
 */

class BabyController extends Controller
{
    /** @var Baby Instância do modelo Baby. */
    private Baby $babyModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // A chamada parent::__construct() não é necessária se App\Core\Controller não tiver um construtor.
        // Se App\Core\Controller tiver um construtor, ele deve ser chamado aqui: parent::__construct();

        // Redireciona para login se o usuário não estiver logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit;
        }
        $this->babyModel = new Baby();
    }

    /**
     * Exibe o formulário de cadastro de um novo bebê.
     *
     * @return void
     */
    public function createForm(): void
    {
        $this->view('Baby/create');
    }

    /**
     * Processa o cadastro de um novo bebê.
     *
     * @return void
     */
    public function save(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'name' => trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'birth_date' => trim(filter_input(INPUT_POST, 'birth_date') ?? ''),
                'gender' => trim(filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'weight' => filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]),
                'height' => filter_input(INPUT_POST, 'height', FILTER_VALIDATE_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]),
                'mother_name' => trim(filter_input(INPUT_POST, 'mother_name', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'father_name' => trim(filter_input(INPUT_POST, 'father_name', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'maternity' => trim(filter_input(INPUT_POST, 'maternity', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'characteristics' => trim(filter_input(INPUT_POST, 'characteristics', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''),
                'image_path' => null, // Será definido após o upload
                // civil_registration será gerado após o insert
            ];

            $errors = $this->validateBabyData($data, $_FILES['image'] ?? null);

            // Upload da Imagem
            if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                // PATH_ROOT já é /public_html, então o diretório de upload é relativo a ele.
                $uploadDir = PATH_ROOT . '/uploads/baby_images/'; 
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0775, true)) {
                        $errors[] = "Falha ao criar diretório de uploads.";
                    }
                }

                if (empty($errors)) { // Verifica se o mkdir falhou
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $errors[] = "Formato de imagem inválido. Apenas JPG, JPEG, PNG, GIF são permitidos.";
                    } else {
                        $imageName = Uuid::uuid4()->toString() . '.' . $fileExtension;
                        $uploadFile = $uploadDir . $imageName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                            // Salva o caminho relativo à raiz do site, acessível publicamente
                            $data['image_path'] = '/uploads/baby_images/' . $imageName;
                        } else {
                            $errors[] = "Erro ao fazer upload da imagem.";
                        }
                    }
                }
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['image']['error'] != UPLOAD_ERR_OK) {
                $errors[] = "Erro no upload da imagem: código " . $_FILES['image']['error'];
            }


            if (empty($errors)) {
                // Geração do registration_number (UUID v4)
                $data['registration_number'] = Uuid::uuid4()->toString();
                $data['civil_registration'] = null; // Inicializa como null

                $babyId = $this->babyModel->create($data);

                if ($babyId) {
                    // Geração e atualização do civil_registration
                    $createdAt = new \DateTime();
                    $civilRegistrationString = $babyId . $createdAt->format('YmdHis') . $data['user_id'];
                    
                    if (!$this->babyModel->updateCivilRegistration($babyId, $civilRegistrationString)) {
                        // Logar erro, mas não necessariamente impedir o fluxo principal
                        error_log("Falha ao atualizar civil_registration para o bebê ID: " . $babyId);
                    }

                    // Redirecionar para a página de detalhes do bebê
                    header('Location: /baby/' . $data['registration_number']);
                    exit;
                } else {
                    $errors[] = "Erro ao cadastrar o bebê. Tente novamente.";
                }
            }

            $this->view('Baby/create', ['errors' => $errors, 'post' => $data]);
        } else {
            // Se não for POST ou usuário não logado, redireciona para o formulário
            header('Location: /baby/new');
            exit;
        }
    }

    /**
     * Exibe os detalhes de um bebê reborn com base no número de registro (RF05).
     *
     * @param string $registration_number O número de registro do bebê.
     * @return void
     */
    public function show(string $registration_number): void
    {
        $baby = $this->babyModel->findByRegistrationNumber($registration_number);
        if ($baby) {
            $this->view('Baby/show', ['baby' => $baby]);
        } else {
            // TODO: Melhorar página de erro 404, talvez com uma view específica
            http_response_code(404);
            $this->view('Errors/404', ['message' => 'Bebê não encontrado.']);
        }
    }

    /**
     * Valida os dados do formulário de cadastro do bebê.
     *
     * @param array $data Dados do formulário.
     * @param array|null $fileData Dados do arquivo de imagem.
     * @return array Array de erros, se houver.
     */
    private function validateBabyData(array $data, ?array $fileData): array
    {
        $errors = [];
        // Validações conforme RF03
        if (empty($data['name'])) $errors[] = "Nome do bebê é obrigatório.";
        if (empty($data['birth_date'])) {
            $errors[] = "Data de nascimento é obrigatória.";
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_date'])) {
            $errors[] = "Formato da data de nascimento inválido. Use YYYY-MM-DD.";
        }
        if (empty($data['gender'])) $errors[] = "Gênero é obrigatório.";
        if ($data['weight'] === false || $data['weight'] <= 0) $errors[] = "Peso inválido ou não informado.";
        if ($data['height'] === false || $data['height'] <= 0) $errors[] = "Altura inválida ou não informada.";
        if (empty($data['maternity'])) $errors[] = "Maternidade (Artesão) é obrigatória.";

        // Validação da imagem (opcional, mas se enviada, deve ser válida)
        if ($fileData && $fileData['error'] == UPLOAD_ERR_OK) {
            if ($fileData['size'] > 2 * 1024 * 1024) { // 2MB
                $errors[] = "A imagem não pode ser maior que 2MB.";
            }
            // Outras validações de tipo de arquivo já são feitas na seção de upload.
        } elseif ($fileData && $fileData['error'] != UPLOAD_ERR_NO_FILE && $fileData['error'] != UPLOAD_ERR_OK) {
             $errors[] = "Ocorreu um erro com o upload da imagem.";
        }

        return $errors;
    }
}