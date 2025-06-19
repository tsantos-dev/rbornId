<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>R-Born IDDados do seu Bebê</title>
    <link rel="stylesheet" href="/src/css/baby_detail.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="sky" id="sky">
        <h1 id="logo" class="d-flex justify-content-center align-items-center mt-2 text-center mx-auto">
            <img src="/src/images/logo_rbornId.png" alt="R-Born Id Logo" class="logo" />
            <span>R-Born ID</span>
        </h1>
        <div class="moon"></div>

        <!-- Constelação exemplo -->
        <div class="constellation">
            <div class="constellation-star" style="top: 0; left: 0"></div>
            <div class="constellation-star" style="top: -20px; left: 30px"></div>
            <div class="constellation-star" style="top: 10px; left: 60px"></div>
            <div class="constellation-star" style="top: -10px; left: 90px"></div>
            <div class="constellation-line" style="top: 2px; left: 4px; width: 28px; transform: rotate(-30deg)"></div>
            <div class="constellation-line" style="top: -18px; left: 34px; width: 32px; transform: rotate(45deg)"></div>
            <div class="constellation-line" style="top: -8px; left: 64px; width: 28px; transform: rotate(-15deg)"></div>
        </div>

        <div class="container w-75 mx-auto absolute-center">
            <?php if (isset($baby) && !empty($baby)): ?>
            <h2 class="mb-2 text-center">Olá, eu sou <i
                    class="text-secondary"><?php echo htmlspecialchars($baby['name']); ?></i>.</h2>
            <h5 class="mb-4 text-center">Abaixo segue meus dados.</h5>

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
                    <img src="/src/images/placeholder_baby.jpeg" alt="Sem foto" class="baby-image" />
                    <p><small>Imagem não disponível.</small></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <dl class="row">

                        <dt class="col-sm-4">Data de Nascimento:</dt>
                        <dd class="col-sm-8">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($baby['birth_date']))); ?>
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

                        <dt class="col-sm-4 mt-3 text-secondary">Código de validação:</dt>
                        <dd class="col-sm-8 mt-3  text-secondary">
                            <?php echo htmlspecialchars($baby['registration_number']); ?></dd>

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
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/src/js/baby_detail.js"></script>
</body>

</html>