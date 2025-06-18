<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Baby;
use App\Models\ApiKey;

class ApiController extends Controller
{
    private Baby $babyModel;
    private ApiKey $apiKeyModel;

    public function __construct()
    {
        // Não iniciar sessão aqui, pois a API é stateless (ou usa autenticação de token)
        // parent::__construct(); // Se o Controller base tiver um construtor

        $this->babyModel = new Baby();
        $this->apiKeyModel = new ApiKey(); // Instanciar o ApiKeyModel
    }

    /**
     * Middleware para autenticação da API.
     * Verifica a chave API no header Authorization.
     *
     * @return bool True se autenticado, false caso contrário (e envia resposta de erro).
     */
    private function authenticate(): bool
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            $this->jsonResponse(['error' => 'Authorization header missing'], 401);
            return false;
        }

        // Espera-se "Bearer YOUR_API_KEY"
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $apiKey = $matches[1];
            if ($this->apiKeyModel->isValid($apiKey)) { // isValid() precisa ser implementado no ApiKeyModel
                return true;
            }
        }

        $this->jsonResponse(['error' => 'Invalid or missing API Key'], 401);
        return false;
    }

    /**
     * Endpoint: GET /api/babies/{civil_registration}
     * Retorna dados do bebê.
     *
     * @param string $civil_registration
     * @return void
     */
    public function getBaby(string $civil_registration): void
    {
        // Este endpoint é público, não requer autenticação por chave API.
        // if (!$this->authenticate()) {
        // }

        $baby = $this->babyModel->findByCivilRegistration($civil_registration);

        if ($baby) {
            // Selecionar apenas os campos relevantes para a API
            $responseData = [
                'civil_registration' => $baby['civil_registration'],
                'name' => $baby['name'],
                'birth_date' => $baby['birth_date'],
                'maternity' => $baby['maternity'],
                'gender' => $baby['gender'],
                'weight' => $baby['weight'],
                'height' => $baby['height'],
                // Adicionar outros campos públicos conforme necessário
            ];
            $this->jsonResponse($responseData, 200);
        } else {
            $this->jsonResponse(['error' => 'Baby not found'], 404);
        }
    }

    /**
     * Endpoint: GET /api/validate/{civil_registration}
     * Valida a existência do registro.
     *
     * @param string $civil_registration
     * @return void
     */
    public function validateRegistration(string $civil_registration): void
    {
        // Este endpoint é público, não requer autenticação por chave API.
        // if (!$this->authenticate()) {
        // }

        $baby = $this->babyModel->findByCivilRegistration($civil_registration);

        if ($baby) {
            $this->jsonResponse(['status' => 'valid', 'civil_registration' => $civil_registration], 200);
        } else {
            $this->jsonResponse(['status' => 'invalid', 'civil_registration' => $civil_registration, 'error' => 'Registration not found'], 404);
        }
    }

    private function jsonResponse(array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}