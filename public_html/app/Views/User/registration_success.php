<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Realizado com Sucesso - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .container {
        max-width: 500px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-3">Cadastro Quase Completo!</h1>
        <p class="lead">Um e-mail de confirmação foi enviado para
            <strong><?php echo htmlspecialchars($email ?? 'seu endereço de e-mail'); ?></strong>.</p>
        <p>Por favor, verifique sua caixa de entrada (e spam) e clique no link enviado para ativar sua conta.</p>
        <a href="/" class="btn btn-primary mt-3">Voltar para a Página Inicial</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>