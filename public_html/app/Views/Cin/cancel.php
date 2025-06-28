<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cancelado - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icon-cancel {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-cancel">&#10006;</div>
        <h1 class="mb-3 text-danger">Pagamento Cancelado</h1>
        <p class="lead"><?php echo htmlspecialchars($message ?? 'O processo de pagamento foi cancelado.'); ?></p>
        <p>Se você mudou de ideia, pode tentar novamente a qualquer momento.</p>
        <p class="text-muted"><small>ID da Sessão: <?php echo htmlspecialchars($sessionId ?? 'N/A'); ?></small></p>
        <a href="/dashboard" class="btn btn-secondary mt-4">Voltar para o Painel</a>
        <a href="/cin/request/<?php echo htmlspecialchars($_SESSION['last_baby_registration_number'] ?? ''); ?>" class="btn btn-primary mt-4 ms-2">Tentar Novamente</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>