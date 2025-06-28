<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BabyCinDocumentModel;
use App\Models\PaymentModel;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    private PaymentModel $paymentModel;
    private BabyCinDocumentModel $babyCinDocumentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->babyCinDocumentModel = new BabyCinDocumentModel();
    }

    public function handle(): void
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Payload inválido
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Assinatura inválida
            http_response_code(400);
            exit();
        }

        // Lidar com o evento
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object; // contém um \Stripe\Checkout\Session
                $this->handleCheckoutSessionCompleted($session);
                break;
            // ... adicione outros tipos de eventos que você queira manipular
            default:
                // Evento não esperado
        }

        http_response_code(200);
    }

    /**
     * Manipula o evento de checkout bem-sucedido.
     *
     * @param \Stripe\Checkout\Session $session
     */
    private function handleCheckoutSessionCompleted(\Stripe\Checkout\Session $session): void
    {
        $sessionId = $session->id;
        $paymentIntentId = $session->payment_intent;

        // Encontra o pagamento em nosso banco de dados
        $payment = $this->paymentModel->findBySessionId($sessionId);

        if ($payment && $payment['status'] === 'pending') {
            // Atualiza o status do pagamento para 'succeeded'
            $this->paymentModel->updateStatusBySessionId($sessionId, 'succeeded', $paymentIntentId);

            // Se o pagamento foi para uma CIN, atualiza as datas do documento
            if ($payment['document_type'] === 'cin') {
                $cinData = $this->babyCinDocumentModel->findByBabyId((int)$payment['baby_id']);
                if ($cinData) {
                    // Define a validade do documento (ex: 10 anos)
                    $issueDate = date('Y-m-d');
                    $expiryDate = date('Y-m-d', strtotime('+10 years'));
                    $this->babyCinDocumentModel->updateDates((int)$cinData['id'], $issueDate, $expiryDate);
                }
            }

            // TODO: Enviar e-mail de confirmação para o usuário.
            // $user = (new \App\Models\User())->findById((int)$payment['user_id']);
            // if ($user) {
            //     $mailService = new \App\Core\MailService();
            //     $mailService->send($user['email'], 'Pagamento Confirmado - R-Born Id', 'Seu pagamento foi confirmado e seu documento está disponível.');
            // }
        }
    }
}