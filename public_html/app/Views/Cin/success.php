<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Confirmado - R-Born Id</title>
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
        .icon-success {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-success">&#10004;</div>
        <h1 class="mb-3 text-success">Pagamento Confirmado!</h1>
        <p class="lead"><?php echo htmlspecialchars($message ?? 'Seu pagamento foi processado com sucesso.'); ?></p>
        <p>Você receberá um e-mail de confirmação em breve com os próximos passos para acessar sua CIN.</p>
        <p class="text-muted"><small>ID da Sessão: <?php echo htmlspecialchars($sessionId ?? 'N/A'); ?></small></p>
        <a href="/dashboard" class="btn btn-primary mt-4">Voltar para o Painel</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>