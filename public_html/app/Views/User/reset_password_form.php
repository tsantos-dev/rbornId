<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 100px; max-width: 450px; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center">Redefinir Senha</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/user/update-password" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
            
            <div class="mb-3">
                <label for="password" class="form-label">Nova Senha:</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                <div id="passwordHelpBlock" class="form-text">
                    Mínimo 8 caracteres, incluindo letras, números e caracteres especiais.
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Nova Senha:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
        </form>
        <p class="mt-3 text-center">
            <a href="/user/login">Voltar para o Login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>