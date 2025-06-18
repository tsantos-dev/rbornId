<?php
// router.php
$requestedResource = $_SERVER['REQUEST_URI'];
// Remove query string from URI for file checking
if (($qpos = strpos($requestedResource, '?')) !== false) {
    $requestedResource = substr($requestedResource, 0, $qpos);
}

$filePath = __DIR__ . '/public_html' . $requestedResource;

if ($requestedResource !== '/' && is_file($filePath)) {
    // Check for common static file extensions and serve them directly
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'application/font-woff',
        'woff2' => 'application/font-woff2',
        'ttf' => 'application/font-ttf',
        'eot' => 'application/vnd.ms-fontobject',
        // Adicione mais tipos MIME conforme necessário
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
        readfile($filePath);
        exit;
    }
    // Se for um arquivo mas não um tipo estático conhecido, pode cair para o roteador da aplicação
    // ou você pode decidir retornar false aqui (o que provavelmente resultaria em 404).
}

// Caso contrário, encaminha para o index.php para que o roteador da aplicação lide com a URL.
$_GET['url'] = ltrim($requestedResource, '/');
if ($_GET['url'] === '') { // Garante que a raiz seja tratada corretamente
    $_GET['url'] = '/';
}
require_once __DIR__ . '/public_html/index.php';