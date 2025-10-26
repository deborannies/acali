<?php

namespace App\Controllers;

use App\Models\User;
use Core\Constants\Constants;
use Lib\FlashMessage;

class BaseController
{
    protected ?User $currentUser = null;

    protected function currentUser(): ?User
    {
        if ($this->currentUser === null && isset($_SESSION['user']['id'])) {
            $this->currentUser = User::find($_SESSION['user']['id']);
        }
        return $this->currentUser;
    }

    protected function authenticated(): void
    {
        if ($this->currentUser() === null) {
            FlashMessage::danger('Você deve estar logado para acessar essa página.');
            $this->redirectToRoute('login.form');
            exit;
        }
    }

    protected function isAdmin(): bool
    {
        $user = $this->currentUser();
        return $user && $user->getRole() === 'admin';
    }

    protected function adminOnly(): void
    {
        if (!$this->isAdmin()) {
            FlashMessage::danger('Você não tem permissão para acessar essa página.');
            $this->redirectToRoute('projects.index');
            exit;
        }
    }

    /**
     * @param string $view
     * @param array<string, mixed> $data
     * @param string $layout
     */
    protected function render(string $view, array $data = [], string $layout = 'application'): void
    {
        $this->currentUser();

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