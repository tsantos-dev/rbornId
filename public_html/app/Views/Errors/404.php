<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Não Encontrada - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .container { max-width: 600px; }
        .status-code { font-size: 6rem; font-weight: bold; color: #6c757d; }
        .message { font-size: 1.5rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-code">404</div>
        <p class="message">
            <?php echo htmlspecialchars($message ?? 'Oops! A página que você está procurando não foi encontrada.'); ?>
        </p>
        <p>Parece que o link que você seguiu está quebrado ou a página foi removida.</p>
        <a href="/" class="btn btn-primary">Voltar para a Página Inicial</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>