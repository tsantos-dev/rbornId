<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar CIN para <?php echo htmlspecialchars($baby['name']); ?> - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            max-width: 700px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-2 text-center">Solicitar CIN</h1>
        <h4 class="mb-4 text-center text-secondary">para <?php echo htmlspecialchars($baby['name']); ?></h4>

        <p class="text-muted">Preencha os dados abaixo para constarem na Carteira de Identidade Nacional (CIN) do seu bebê. Após o preenchimento, você será direcionado para a página de pagamento.</p>

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

        <form action="/cin/process/<?php echo htmlspecialchars($baby['registration_number']); ?>" method="POST">
            
            <h5 class="mt-4">Dados Pessoais Adicionais</h5>
            <hr>

            <div class="mb-3">
                <label for="social_name" class="form-label">Nome Social (Opcional):</label>
                <input type="text" class="form-control" id="social_name" name="social_name" value="<?php echo htmlspecialchars($post['social_name'] ?? $cinData['social_name'] ?? ''); ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="place_of_birth_city" class="form-label">Cidade de Nascimento:</label>
                    <input type="text" class="form-control" id="place_of_birth_city" name="place_of_birth_city" value="<?php echo htmlspecialchars($post['place_of_birth_city'] ?? $cinData['place_of_birth_city'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="place_of_birth_state" class="form-label">Estado de Nascimento (UF):</label>
                    <select class="form-select" id="place_of_birth_state" name="place_of_birth_state" required>
                        <option value="">Selecione...</option>
                        <?php 
                            $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                            $selectedUf = $post['place_of_birth_state'] ?? $cinData['place_of_birth_state'] ?? '';
                            foreach ($ufs as $uf) {
                                echo "<option value=\"$uf\"" . ($selectedUf === $uf ? ' selected' : '') . ">$uf</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="nationality" class="form-label">Nacionalidade:</label>
                <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo htmlspecialchars($post['nationality'] ?? $cinData['nationality'] ?? 'Brasileira'); ?>" required>
            </div>

            <h5 class="mt-4">Informações de Saúde (Opcional)</h5>
            <p class="form-text">Estas informações só serão incluídas na CIN se você preenchê-las.</p>
            <hr>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="blood_type" class="form-label">Tipo Sanguíneo:</label>
                    <select class="form-select" id="blood_type" name="blood_type">
                        <option value="">Não informar</option>
                        <?php 
                            $bloodTypes = ['A', 'B', 'AB', 'O'];
                            $selectedBloodType = $post['blood_type'] ?? $cinData['blood_type'] ?? '';
                            foreach ($bloodTypes as $type) {
                                echo "<option value=\"$type\"" . ($selectedBloodType === $type ? ' selected' : '') . ">$type</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rh_factor" class="form-label">Fator RH:</label>
                    <select class="form-select" id="rh_factor" name="rh_factor">
                        <option value="">Não informar</option>
                        <?php 
                            $rhFactors = ['Positivo', 'Negativo'];
                            $selectedRhFactor = $post['rh_factor'] ?? $cinData['rh_factor'] ?? '';
                            foreach ($rhFactors as $factor) {
                                echo "<option value=\"$factor\"" . ($selectedRhFactor === $factor ? ' selected' : '') . ">$factor</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="health_conditions" class="form-label">Condições Específicas de Saúde:</label>
                <textarea class="form-control" id="health_conditions" name="health_conditions" rows="3" placeholder="Ex: Alergia a dipirona, Asma, etc."><?php echo htmlspecialchars($post['health_conditions'] ?? $cinData['health_conditions'] ?? ''); ?></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="/baby/<?php echo htmlspecialchars($baby['registration_number']); ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar e Continuar para Pagamento</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>