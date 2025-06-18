<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Bebê - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 30px;
        margin-bottom: 30px;
        max-width: 700px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4 text-center">Cadastre seu Bebê</h1>

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

        <form action="/baby/save" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nome completo:</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="<?php echo htmlspecialchars($post['name'] ?? ''); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="birth_date" class="form-label">Data de Nascimento:</label>
                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                        value="<?php echo htmlspecialchars($post['birth_date'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gênero:</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="">Selecione...</option>
                        <option value="Masculino"
                            <?php echo (isset($post['gender']) && $post['gender'] === 'Masculino') ? 'selected' : ''; ?>>
                            Masculino</option>
                        <option value="Feminino"
                            <?php echo (isset($post['gender']) && $post['gender'] === 'Feminino') ? 'selected' : ''; ?>>
                            Feminino</option>
                        <option value="Outro"
                            <?php echo (isset($post['gender']) && $post['gender'] === 'Outro') ? 'selected' : ''; ?>>
                            Outro</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="weight" class="form-label">Peso (kg):</label>
                    <input type="number" step="0.001" class="form-control" id="weight" name="weight"
                        value="<?php echo htmlspecialchars($post['weight'] ?? ''); ?>" placeholder="Ex: 1.250" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="height" class="form-label">Altura (m):</label>
                    <input type="number" step="0.01" class="form-control" id="height" name="height"
                        value="<?php echo htmlspecialchars($post['height'] ?? ''); ?>" placeholder="Ex: 0.50" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="maternity" class="form-label">Maternidade (local de "nascimento"):</label>
                <input type="text" class="form-control" id="maternity" name="maternity"
                    value="<?php echo htmlspecialchars($post['maternity'] ?? ''); ?>" required>
            </div>

            <h5 class="mt-4">Filiação (Opcional)</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="mother_name" class="form-label">Nome da Mãe:</label>
                    <input type="text" class="form-control" id="mother_name" name="mother_name"
                        value="<?php echo htmlspecialchars($post['mother_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="father_name" class="form-label">Nome do Pai:</label>
                    <input type="text" class="form-control" id="father_name" name="father_name"
                        value="<?php echo htmlspecialchars($post['father_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="characteristics" class="form-label">Características Especiais (Opcional):</label>
                <textarea class="form-control" id="characteristics" name="characteristics"
                    rows="3"><?php echo htmlspecialchars($post['characteristics'] ?? ''); ?></textarea>
            </div>


            <div class="mb-3">
                <label for="image" class="form-label">Foto do Bebê (Opcional - JPG, PNG, GIF - Máx 2MB):</label>
                <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Cadastrar Bebê</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>