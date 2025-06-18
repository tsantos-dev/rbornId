<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - R-Born Id</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    body {
        background-color: #f8f9fa;
        background-image: url('https://www.atribuna.com.br/image/policy:1.461845:1748258428/image.jpg?&f=default');
        /* Ajuste o caminho conforme necessário */
        background-size: cover;
        background-position: left;
        background-repeat: no-repeat;
    }

    .container {
        margin-top: 100px;
        max-width: 400px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4 text-center">Login</h1>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="/user/authenticate" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?php echo htmlspecialchars($post['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <p class="mt-3 text-center"><a href="/user/forgot-password">Esqueceu sua senha?</a></p>
        <!-- TODO: Implementar recuperação de senha -->
        <p class="text-center">Não tem uma conta? <a href="/user/register">Cadastre-se</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>