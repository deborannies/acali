<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate(): void
    {
        // Usuário Admin
        $admin = new User([
            'name' => 'Administrador',
            'email' => 'admin@teste.com',
            'password' => '123456',
            'role' => 'admin'
        ]);
        $admin->save();

        echo "✅ Usuário admin 'admin@teste.com' criado com sucesso!\n";

        // Usuário Comum
        $user = new User([
            'name' => 'Usuário Comum',
            'email' => 'user@teste.com',
            'password' => '123456',
            'role' => 'user'
        ]);
        $user->save();

        echo "✅ Usuário comum 'user@teste.com' criado com sucesso!\n";
    }
}