<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Request;
use Lib\FlashMessage;

class AuthenticationsController extends BaseController
{
    public function new(Request $request): void
    {
        $title = 'Login - ACALI';
        $this->render('authentications/new', compact('title'), 'login');
    }

    public function authenticate(Request $request): void
    {
        $params = $request->getParams();
        $email = $params['user']['email'] ?? '';
        $password = $params['user']['password'] ?? '';

        $user = User::findByEmail($email);

        if ($user && $user->authenticate($password)) {
            $_SESSION['user'] = [
                'id' => $user->getId(),
                'role' => $user->getRole()
            ];
            FlashMessage::success('Login realizado com sucesso!');
            $this->redirectToRoute('projects.index');
        } else {
            FlashMessage::danger('E-mail ou senha incorretos.');
            $this->redirectToRoute('login.form');
        }
    }

    public function destroy(): void
    {
        unset($_SESSION['user']);
        FlashMessage::success('Logout realizado com sucesso!');
        $this->redirectToRoute('login.form');
    }
}
