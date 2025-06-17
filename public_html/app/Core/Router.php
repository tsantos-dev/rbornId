<?php

namespace App\Core;

/**
 * Class Router
 *
 * Um roteador simples para direcionar requisições para controllers e actions.
 */
class Router
{
    /** @var array Rotas registradas. */
    protected array $routes = [];

    /** @var array Parâmetros extraídos da URI. */
    protected array $params = [];

    /**
     * Adiciona uma rota à tabela de roteamento.
     *
     * @param string $method Método HTTP (GET, POST, etc.).
     * @param string $route Padrão da rota (ex: '/users/{id}').
     * @param string $handler Controller e método (ex: 'UserController@show').
     * @return void
     */
    public function addRoute(string $method, string $route, string $handler): void
    {
        $this->routes[strtoupper($method)][$route] = $handler;
    }

    /**
     * Converte uma string de rota em uma expressão regular.
     *
     * @param string $route A rota a ser convertida.
     * @return string A expressão regular.
     */
    private function convertRouteToRegex(string $route): string
    {
        // Converte variáveis como {id} para named capture groups (?<id>[a-zA-Z0-9_]+)
        $route = preg_replace('/\{([a-z_]+)}/', '(?P<\1>[a-zA-Z0-9_.-]+)', $route);
        // Adiciona delimitadores e âncoras
        return "@^" . $route . "$@i";
    }

    /**
     * Tenta encontrar uma rota que corresponda à URI e ao método fornecidos.
     *
     * @param string $method O método HTTP da requisição.
     * @param string $uri A URI da requisição.
     * @return string|false O manipulador da rota correspondente ou false se não houver correspondência.
     */
    protected function match(string $method, string $uri): string|false
    {
        $uri = trim($uri, '/');
        if (empty($uri)) $uri = '/'; // Trata a raiz

        if (!isset($this->routes[$method])) {
            return false;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            $routePattern = $this->convertRouteToRegex(trim($route, '/'));
            if (preg_match($routePattern, $uri, $matches)) {
                // Extrai os parâmetros nomeados
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $this->params[$key] = $match;
                    }
                }
                return $handler;
            }
        }
        return false;
    }

    /**
     * Despacha a requisição para o controller e método apropriados.
     *
     * @param string $requestMethod O método HTTP da requisição.
     * @param string $requestUri A URI da requisição.
     * @return void
     */
    public function dispatch(string $requestMethod, string $requestUri): void
    {
        $handler = $this->match(strtoupper($requestMethod), $requestUri);

        if ($handler === false) {
            // Rota não encontrada - pode ser um controller/método padrão ou uma página 404
            http_response_code(404);
            echo "404 - Página não encontrada"; // Ou renderizar uma view de 404
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $controllerClass = "App\Controllers\" . $controllerName;

        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $methodName)) {
                call_user_func_array([$controllerInstance, $methodName], $this->params);
            } else {
                throw new \Exception("Método {$methodName} não encontrado no controller {$controllerClass}");
            }
        } else {
            throw new \Exception("Controller {$controllerClass} não encontrado");
        }
    }
}