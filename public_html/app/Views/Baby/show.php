<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Bebê: <?php echo htmlspecialchars($baby['name'] ?? 'Bebê'); ?> - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 30px;
        margin-bottom: 30px;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .baby-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 20px;
        max-height: 400px;
        object-fit: cover;
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
    <div class="container">
        <?php if (isset($baby) && !empty($baby)): ?>
        <h1 class="mb-4 text-center">Detalhes de <?php echo htmlspecialchars($baby['name']); ?></h1>

        <div class="row">
            <div class="col-md-4 text-center">
                <?php
                        // DEBUGGING:
                        // $imagePathForFileExists = PATH_ROOT . $baby['image_path'];
                        // echo "<p><small>Verificando arquivo: " . htmlspecialchars($imagePathForFileExists) . "<br>Existe? " . (file_exists($imagePathForFileExists) ? 'Sim' : 'Não') . "</small></p>";
                    ?>
                <?php if (!empty($baby['image_path']) && file_exists(PATH_ROOT . $baby['image_path'])): ?>
                <img src="/<?php echo ltrim(htmlspecialchars($baby['image_path']), '/'); ?>"
                    alt="Foto de <?php echo htmlspecialchars($baby['name']); ?>" class="baby-image" />
                <?php else: ?>
                <img src="/path/to/default/placeholder_image.png" alt="Sem foto" class="baby-image" />
                <p><small>Imagem não disponível.</small></p>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-4">Nº de Registro (Sistema):</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['registration_number']); ?></dd>

                    <dt class="col-sm-4">Data de Nascimento:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars(date('d/m/Y', strtotime($baby['birth_date']))); ?>
                    </dd>

                    <dt class="col-sm-4">Gênero:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['gender']); ?></dd>

                    <dt class="col-sm-4">Peso:</dt>
                    <dd class="col-sm-8">
                        <?php echo htmlspecialchars(number_format((float)$baby['weight'], 3, ',', '.')); ?> kg</dd>

                    <dt class="col-sm-4">Altura:</dt>
                    <dd class="col-sm-8">
                        <?php echo htmlspecialchars(number_format((float)$baby['height'], 2, ',', '.')); ?> m</dd>

                    <dt class="col-sm-4">Maternidade:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['maternity']); ?></dd>

                    <?php if (!empty($baby['mother_name'])): ?>
                    <dt class="col-sm-4">Nome da Mãe:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['mother_name']); ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($baby['father_name'])): ?>
                    <dt class="col-sm-4">Nome do Pai:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['father_name']); ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($baby['civil_registration'])): ?>
                    <dt class="col-sm-4">Registro Civil:</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($baby['civil_registration']); ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($baby['characteristics'])): ?>
                    <dt class="col-sm-4">Características Especiais:</dt>
                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($baby['characteristics'])); ?></dd>
                    <?php endif; ?>
                </dl>
                <a href="/" class="btn btn-secondary mt-3">Voltar para Início</a>
                <a href="/document/birth-certificate/<?php echo htmlspecialchars($baby['registration_number']); ?>"
                    class="btn btn-primary mt-3">Gerar Certidão de Nascimento</a>
            </div>
        </div>
        <?php else: ?>
        <h1 class="mb-4 text-center text-danger">Bebê não encontrado</h1>
        <p class="text-center">O bebê que você está procurando não foi encontrado em nosso sistema.</p>
        <div class="text-center">
            <a href="/" class="btn btn-primary mt-3">Voltar para Início</a>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>