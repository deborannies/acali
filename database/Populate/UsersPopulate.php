<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate(): void
    {
        // Usuário Admin
        $admin = new User();
        $admin->name = 'Administrador';
        $admin->email = 'admin@teste.com';
        $admin->password = '123456';
        $admin->role = 'admin';
        $admin->save();

        echo "✅ Usuário admin 'admin@teste.com' criado com sucesso!\n";

        // Usuário Comum
        $user = new User();
        $user->name = 'Usuário Comum';
        $user->email = 'user@teste.com';
        $user->password = '123456';
        $user->role = 'user';
        $user->save();

        echo "✅ Usuário comum 'user@teste.com' criado com sucesso!\n";
    }
}
