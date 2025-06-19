<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Baby;

class DashboardController extends Controller
{
    private Baby $babyModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /user/login');
            exit;
        }
        $this->babyModel = new Baby();
    }

    /**
     * Exibe a página principal do dashboard do usuário.
     *
     * @return void
     */
    public function index(): void
    {
        $userId = $_SESSION['user_id'];
        $babies = $this->babyModel->findByUserId($userId);

        $this->view('Dashboard/index', ['babies' => $babies, 'userName' => $_SESSION['user_name'] ?? 'Usuário']);
    }
}