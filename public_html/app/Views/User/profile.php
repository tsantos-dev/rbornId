<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .navbar {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
    }

    .profile-container {
        margin-top: 30px;
        max-width: 600px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    dt {
        font-weight: bold;
    }

    dd {
        margin-bottom: 0.5rem;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">R-Born Id</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            Olá, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger" href="/user/logout">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container profile-container">
        <h1 class="mb-4 text-center">Meu Perfil</h1>

        <?php if (isset($user) && !empty($user)): ?>
        <dl class="row">
            <dt class="col-sm-3">Nome:</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['name']); ?></dd>

            <dt class="col-sm-3">E-mail:</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>

            <dt class="col-sm-3">CPF:</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars($user['cpf']); ?></dd>

            <dt class="col-sm-3">Membro desde:</dt>
            <dd class="col-sm-9"><?php echo htmlspecialchars(date('d/m/Y', strtotime($user['created_at']))); ?></dd>

            <dt class="col-sm-3">E-mail Verificado:</dt>
            <dd class="col-sm-9">
                <?php echo !empty($user['email_verified_at']) ? 'Sim' : 'Não (Verifique seu e-mail)'; ?></dd>
        </dl>
        <div class="text-center mt-4">
            <a href="/dashboard" class="btn btn-secondary">Voltar ao Painel</a>
            <button class="btn btn-primary" disabled>Editar Perfil (Em breve)</button>
            <button class="btn btn-warning" disabled>Alterar Senha (Em breve)</button>
        </div>
        <?php else: ?>
        <p class="text-danger">Não foi possível carregar os dados do seu perfil.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>