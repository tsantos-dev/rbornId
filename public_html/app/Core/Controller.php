<?php

namespace App\Core;

/**
 * Class Controller
 *
 * Controller base para a aplicação.
 * Pode ser estendido para adicionar funcionalidades comuns a todos os controllers.
 */
abstract class Controller
{
    /**
     * Carrega uma view.
     *
     * @param string $viewName O nome do arquivo da view (sem .php). Ex: 'User/register'
     * @param array $data Dados a serem extraídos e disponibilizados para a view.
     * @return void
     */
    protected function view(string $viewName, array $data = []): void
    {
        extract($data); // Transforma chaves do array em variáveis

        $viewFile = APP_PATH . '/Views/' . $viewName . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            // Em um sistema real, lançar uma exceção ou mostrar uma página de erro 404
            die("View '{$viewName}' não encontrada.");
        }
    }
    // No futuro, podemos adicionar métodos aqui, como:
    // protected function redirect(string $url): void { ... }
}