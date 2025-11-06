<?php

namespace App\Controllers;

use App\Models\User;
use Core\Constants\Constants;
use Lib\FlashMessage;

class BaseController
{
    protected ?User $currentUser = null;

    public function __construct()
    {
        if (isset($_SESSION['user']['id'])) {
            $this->currentUser = User::findById($_SESSION['user']['id']);
        }
    }

    protected function authenticated(): void
    {
        if ($this->currentUser === null) {
            FlashMessage::danger('Você deve estar logado para acessar essa página.');
            $this->redirectToRoute('login.form');
        }
    }

    protected function isAdmin(): bool
    {
        return $this->currentUser && $this->currentUser->getRole() === 'admin';
    }

    protected function adminOnly(): void
    {
        if (!$this->isAdmin()) {
            FlashMessage::danger('Você não tem permissão para acessar essa página.');
            $this->redirectToRoute('projects.index');
        }
    }

    /**
     * @param string $view
     * @param array<string, mixed> $data
     * @param string $layout
     */
    protected function render(string $view, array $data = [], string $layout = 'application'): void
    {
        $viewPath = Constants::rootPath()->join('app/views/' . $view . '.phtml');
        extract($data);
        require Constants::rootPath()->join('app/views/layouts/' . $layout . '.phtml');
    }

    /**
     * @param string $routeName
     * @param array<string, mixed> $params
     */
    protected function redirectToRoute(string $routeName, array $params = []): void
    {
        $location = route($routeName, $params);
        header('Location: ' . $location);
        exit;
    }
}