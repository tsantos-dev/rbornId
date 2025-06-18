<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Baby;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use FPDF; // Usando a classe FPDF globalmente, pois a biblioteca setasign/fpdf a disponibiliza assim.

class DocumentController extends Controller
{
    private Baby $babyModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // A verificação de login será feita por método.
        $this->babyModel = new Baby();
    }

    /**
     * Gera a Certidão de Nascimento em PDF para um bebê.
     *
     * @param string $registration_number O número de registro do bebê.
     * @return void
     */
    public function generateBirthCertificatePdf(string $registration_number): void
    {
        // Protege esta rota específica
        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit;
        }

        $baby = $this->babyModel->findByRegistrationNumber($registration_number);

        if (!$baby) {
            $this->view('Errors/404', ['message' => 'Bebê não encontrado para gerar a certidão.']);
            return;
        }

        // Gerar URL de validação para o QR Code
        $validationUrl = $this->getBaseUrl() . '/baby/' . $baby['registration_number'];

        // Gerar QR Code
        $qrCode = QrCode::create($validationUrl)
            ->setSize(300) // Tamanho em pixels
            ->setMargin(10);
        $writer = new PngWriter();
        $qrCodeImageString = $writer->write($qrCode)->getString();

        // Salvar temporariamente a imagem do QR Code para embutir no PDF
        $qrCodeImagePath = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
        file_put_contents($qrCodeImagePath, $qrCodeImageString);

        // Iniciar geração do PDF com FPDF
        $pdf = new FPDF('P', 'mm', 'A4'); // P: Retrato, mm: milímetros, A4: tamanho
        $pdf->AddPage();

        // CAMINHO PARA SUA IMAGEM DE FUNDO
        $backgroundImagePath_w = PATH_ROOT . '/src/images/bg_certidao_menina.jpg';
        $backgroundImagePath_m = PATH_ROOT . '/src/images/bg_certidao_menino.jpg';
        $backgroundImagePath_o = PATH_ROOT . '/src/images/bg_certidao_outro.jpg';

        // Adicionar imagem de fundo (ajuste as coordenadas e dimensões conforme necessário)
        // X, Y, Largura, Altura (0,0 para canto superior esquerdo, 210x297 para A4 em mm)
        if ($baby['gender'] == 'feminino') {
            $backgroundImagePath = $backgroundImagePath_w;
            $pdf->Image($backgroundImagePath, 0, 0, 210, 297, 'JPG'); // Ou PNG, GIF, etc.
        } else if ($baby['gender'] == 'masculino') {
            $backgroundImagePath = $backgroundImagePath_m;
            $pdf->Image($backgroundImagePath, 0, 0, 210, 297, 'JPG'); // Ou PNG, GIF, etc.
        } else {
            $backgroundImagePath = $backgroundImagePath_o;
            $pdf->Image($backgroundImagePath, 0, 0, 210, 297, 'JPG'); // Ou PNG, GIF, etc.
        }



        $pdf->SetFont('Arial', 'B', 16);

        // Título (Exemplo simples)
        $pdf->Cell(0, 10, utf8_decode('CERTIDÃO DE NASCIMENTO'), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 12);

        // Dados do Bebê (Exemplo)
        $pdf->Cell(50, 10, utf8_decode('Registro Civil:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode($baby['civil_registration']), 0, 1);

        $pdf->Cell(50, 10, utf8_decode('Nome:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode($baby['name']), 0, 1);

        $pdf->Cell(50, 10, utf8_decode('Nome da mãe:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode($baby['mother_name']), 0, 1);

        $pdf->Cell(50, 10, utf8_decode('Nome do pai:'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode($baby['father_name']), 0, 1);        

        $pdf->Cell(50, 10, utf8_decode('Data de Nascimento:'), 0, 0);
        $pdf->Cell(0, 10, date('d/m/Y', strtotime($baby['birth_date'])), 0, 1);

        $pdf->Cell(50, 10, utf8_decode('Maternidade :'), 0, 0);
        $pdf->Cell(0, 10, utf8_decode($baby['maternity']), 0, 1);

        // Adicionar mais campos conforme necessário (local, filiação, etc.)

        // Adicionar QR Code ao PDF
        // Posição X, Y, Largura (0 para auto-ajuste da altura mantendo proporção)
        $pdf->Image($qrCodeImagePath, $pdf->GetX() + 140, $pdf->GetY() - 30, 40, 0, 'PNG');
        unlink($qrCodeImagePath); // Apagar imagem temporária do QR Code

        $pdf->Ln(20); // Espaçamento
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, utf8_decode('Valide este documento em: ' . $validationUrl), 0, 1, 'C');

        // Saída do PDF para download
        $pdf->Output('D', 'Certidao_Nascimento_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $baby['name']) . '.pdf');
        exit;
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }
}