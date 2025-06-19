<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Painel - R-Born Id</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <style>
    .material-symbols-outlined {
        font-variation-settings:
            'FILL'0,
            'wght'400,
            'GRAD'0,
            'opsz'24
    }

    body {
        background-color: #f3ece6;
        background-image: url('/src/images/bg_dashboard.jpg');
        /* Substitua pelo caminho da sua imagem de fundo */
        background-size: contain;
        background-position: left;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    .navbar {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
    }

    .action-links ul {
        list-style: none;
        padding: 0;
        margin: 0;

        span {
            font-size: 36px;
        }

        a {
            text-decoration: none;
            color: #938c86;
        }
    }

    .dashboard-container {
        margin-top: 20px;
    }


    .action-links .btn {
        margin: 5px;
    }

    .baby-carousel .carousel-item img {
        height: auto;
        max-height: 80vh;
        /* Ajuste conforme necessário */
        object-fit: cover;
        border-radius: .25rem;

    }

    .baby-carousel .carousel-caption {
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: .25rem;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;

    }

    .no-babies {
        text-align: center;
        padding: 20px;
        border: 1px dashed #ccc;
        border-radius: .25rem;
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
                            Olá, <?php echo htmlspecialchars($userName ?? 'Usuário'); ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="" href="/user/logout">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                                <path fill-rule="evenodd"
                                    d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">

        <div class="row">
            <div class="col offset-md-3">
                <?php if (!empty($babies)): ?>
                <div id="babyCarousel" class="carousel slide baby-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($babies as $index => $baby): ?>
                        <div class="card carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <a href="/baby/<?php echo htmlspecialchars($baby['registration_number']); ?>">
                                <?php
                                        $imageSrc = !empty($baby['image_path']) && file_exists(PATH_ROOT . $baby['image_path'])
                                            ? '/' . ltrim(htmlspecialchars($baby['image_path']), '/')
                                            : '/path/to/default/placeholder_image.png'; // Substitua pelo seu placeholder
                                        ?>
                                <img src="<?php echo $imageSrc; ?>" class="d-block w-100"
                                    alt="<?php echo htmlspecialchars($baby['name']); ?>">
                                <div class="carousel-caption d-none d-md-block w-100">
                                    <h5>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                        </svg>
                                        <?php echo htmlspecialchars($baby['name']); ?>
                                    </h5>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#babyCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#babyCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                </div>
                <?php else: ?>
                <div class="no-babies">
                    <p>Você ainda não cadastrou nenhum bebê.</p>
                    <a href="/baby/new" class="btn btn-primary">Cadastrar meu primeiro bebê</a>
                </div>
                <?php endif; ?>
            </div>

            <div class="col col-md-auto offset-md-2">
                <div class="action-links text-left mb-4">
                    <ul>

                        <li class="my-3">
                            <a href="/baby/new" class="icon-link fs-5">
                                <span class="material-symbols-outlined">child_care</span>Cadastrar Novo Bebê
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="/user/profile" class="icon-link fs-5">
                                <span class="material-symbols-outlined">face</span> Meus Dados
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="/user/profile" class="icon-link fs-5">
                                <span class="material-symbols-outlined">badge</span> Novo documento
                            </a>
                        </li>
                        <li>
                            <a class="icon-link fs-5" href="/user/logout">
                                <span class="material-symbols-outlined">exit_to_app</span> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>