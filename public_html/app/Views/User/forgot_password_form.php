<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        background-image: url('/src/images/bg_forgot_password.jpg');
        /* Substitua pelo caminho da sua imagem de fundo */
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .container {
        margin-top: 100px;
        max-width: 450px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4 text-center">Recuperar Senha</h1>
        <p class="text-muted text-center mb-4">Digite seu e-mail para enviarmos um link de redefinição de senha.</p>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/user/send-reset-link" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?php echo htmlspecialchars($post['email'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar Link de Redefinição</button>
        </form>
        <p class="mt-3 text-center">
            <a href="/user/login">Voltar para o Login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>