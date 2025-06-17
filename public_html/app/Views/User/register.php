<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - R-Born Id</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 50px;
        max-width: 500px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4 text-center">Cadastro de Novo Usuário</h1>
        <?php // A variável $errors e $post (se houver) são extraídas pelo método view() no Controller ?>
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Ops! Algo deu errado.</h4>
            <p>Por favor, corrija os seguintes erros:</p>
            <hr>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/user/create" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nome Completo:</label>
                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                    id="name" name="name" value="<?php echo htmlspecialchars($post['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                    id="email" name="email" value="<?php echo htmlspecialchars($post['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" class="form-control <?php echo isset($errors['cpf']) ? 'is-invalid' : ''; ?>"
                    id="cpf" name="cpf" value="<?php echo htmlspecialchars($post['cpf'] ?? ''); ?>" required
                    placeholder="000.000.000-00">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha:</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                <div id="passwordHelpBlock" class="form-text">
                    Mínimo 8 caracteres, incluindo letras, números e caracteres especiais.
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmar Senha:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
    </div>

    <!-- Bootstrap JS Bundle (inclui Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>