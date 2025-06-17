<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class MailService
 *
 * Serviço para envio de e-mails utilizando PHPMailer.
 * As configurações de SMTP devem ser definidas aqui ou carregadas de um arquivo de configuração.
 */
class MailService
{
    /** @var PHPMailer Instância do PHPMailer. */
    private PHPMailer $mailer;

    /**
     * Construtor do MailService.
     * Configura a instância do PHPMailer.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true); // true habilita exceções

        // Configurações do Servidor SMTP (substitua com suas credenciais)
        // É altamente recomendável usar variáveis de ambiente ou um arquivo de config separado para isso.
        $this->mailer->isSMTP();
        $this->mailer->Host       = 'smtp.example.com'; // Servidor SMTP
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = 'user@example.com'; // Seu usuário SMTP
        $this->mailer->Password   = 'your_smtp_password'; // Sua senha SMTP
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Ou PHPMailer::ENCRYPTION_SMTPS
        $this->mailer->Port       = 587; // Porta TLS (ou 465 para SMTPS)

        // Remetente
        $this->mailer->setFrom('no-reply@r-born-id.com', 'R-Born Id');

        // Configurações de codificação
        $this->mailer->CharSet = 'UTF-8';
    }

    /**
     * Envia um e-mail.
     *
     * @param string $to Endereço de e-mail do destinatário.
     * @param string $subject Assunto do e-mail.
     * @param string $body Corpo do e-mail (pode ser HTML).
     * @param bool $isHtml Define se o corpo do e-mail é HTML.
     * @return bool Retorna true se o e-mail foi enviado com sucesso, false caso contrário.
     */
    public function send(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            // $this->mailer->AltBody = 'Este é o corpo em texto puro para clientes de e-mail não-HTML'; // Opcional

            return $this->mailer->send();
        } catch (Exception $e) {
            // Em produção, logar o erro em vez de exibi-lo.
            // error_log("Erro ao enviar e-mail: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}