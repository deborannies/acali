<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate(): void
    {
        $user = new User();
        $user->name = 'Usuário de Teste';
        $user->email = 'teste@example.com';

        // Apenas defina a propriedade 'password'
        // O modelo User irá criptografá-la automaticamente
        $user->password = '123456';
        
        // Salva no banco
        $user->save();

        echo "Usuário de teste 'teste@example.com' foi criado com sucesso!\n";
    }
}
