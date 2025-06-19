<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Enviado - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { max-width: 500px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-3">Verifique seu E-mail</h1>
        <p class="lead">Se uma conta com o e-mail <strong><?php echo htmlspecialchars($email ?? 'informado'); ?></strong> existir em nosso sistema, um link para redefinição de senha foi enviado.</p>
        <p>Por favor, verifique sua caixa de entrada (e pasta de spam).</p>
        
        <?php if (isset($dev_token)): // Apenas para desenvolvimento ?>
            <div class="alert alert-info mt-3">
                <p class="mb-1"><strong>Para desenvolvimento:</strong></p>
                <p class="mb-1">O link de redefinição seria enviado para o e-mail acima.</p>
                <p class="mb-0">Você pode usar o seguinte link para testar (copie e cole no navegador):<br>
                <a href="/user/reset-password/<?php echo htmlspecialchars($dev_token); ?>">/user/reset-password/<?php echo htmlspecialchars($dev_token); ?></a></p>
            </div>
        <?php endif; ?>

        <a href="/user/login" class="btn btn-primary mt-3">Voltar para o Login</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>