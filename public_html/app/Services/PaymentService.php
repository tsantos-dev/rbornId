<?php

namespace App\Services;

use App\Models\PaymentModel;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentService
{
    private PaymentModel $paymentModel;

    public function __construct()
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Cria uma sessão de checkout no Stripe e um registro de pagamento pendente no banco.
     *
     * @param array $paymentData Contém informações sobre o pagamento.
     *        Ex: ['userId', 'babyId', 'documentType', 'productName', 'productDescription', 'amountInCents', 'currency']
     * @return Session|null Retorna o objeto da sessão de checkout do Stripe ou null em caso de erro.
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(array $paymentData): ?Session
    {
        $successUrl = $this->getBaseUrl() . '/cin/success?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $this->getBaseUrl() . '/cin/cancel?session_id={CHECKOUT_SESSION_ID}';

        $checkout_session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => $paymentData['currency'] ?? 'brl',
                    'product_data' => [
                        'name' => $paymentData['productName'],
                        'description' => $paymentData['productDescription'],
                    ],
                    'unit_amount' => $paymentData['amountInCents'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [ // Metadados úteis para o webhook
                'user_id' => $paymentData['userId'],
                'baby_id' => $paymentData['babyId'],
                'document_type' => $paymentData['documentType'],
            ]
        ]);

        // Cria o registro de pagamento pendente no nosso banco
        $this->paymentModel->create($paymentData['userId'], $paymentData['babyId'], $paymentData['documentType'], ($paymentData['amountInCents'] / 100), $paymentData['currency'], $checkout_session->id);

        return $checkout_session;
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }
}