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
        // O DocumentController será instanciado apenas quando necessário.
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

        if ($payment && $payment['status'] === 'pending' && isset($session->metadata['user_id'])) {
            // Atualiza o status do pagamento para 'succeeded'
            $this->paymentModel->updateStatusBySessionId($sessionId, 'succeeded', $paymentIntentId);

            // Busca os dados do usuário para enviar o e-mail
            $user = (new \App\Models\User())->findById((int)$session->metadata['user_id']);

            // Se o pagamento foi para uma CIN, atualiza as datas do documento
            if ($user && $payment['document_type'] === 'cin') {
                $cinData = $this->babyCinDocumentModel->findByBabyId((int)$payment['baby_id']);
                if ($cinData) {
                    // Define a validade do documento (ex: 10 anos)
                    $issueDate = date('Y-m-d');
                    $expiryDate = date('Y-m-d', strtotime('+10 years'));
                    $this->babyCinDocumentModel->updateDates((int)$cinData['id'], $issueDate, $expiryDate);

                    // Geração e envio do PDF
                    $documentController = new \App\Controllers\DocumentController();
                    $pdfContent = $documentController->getCinPdfContentForWebhook((int)$payment['baby_id']);

                    if (!empty($pdfContent)) {
                        $baby = (new \App\Models\Baby())->findById((int)$payment['baby_id']);
                        $fileName = 'CIN_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $baby['name'] ?? 'bebe') . '.pdf';

                        $mailService = new \App\Core\MailService();
                        try {
                            $mailService->addAttachmentFromString($pdfContent, $fileName);
                            $mailService->send($user['email'], 'Sua CIN R-Born Id está pronta!', 'Olá ' . $user['name'] . ',<br><br>Seu pagamento foi confirmado e a Carteira de Identidade Nacional (CIN) do seu bebê está anexada a este e-mail.<br><br>Atenciosamente,<br>Equipe R-Born Id');
                        } catch (\Exception $e) {
                            error_log("Erro ao enviar e-mail com anexo da CIN: " . $e->getMessage());
                        }
                    } else {
                        error_log("Falha ao gerar o conteúdo do PDF da CIN para o bebê ID: " . $payment['baby_id']);
                    }
                }
            }
        } else {
            error_log("Erro: pagamento não encontrado ou status não é pendente. Session ID: " . $sessionId);
        }
    }
}