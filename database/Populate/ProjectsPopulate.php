<?php

namespace Database\Populate;

use App\Models\Project;
use App\Models\User; 

class ProjectsPopulate
{
    public static function populate(): void
    {
        $adminUser = User::findBy(['email' => 'admin@teste.com']);
        
        if (!$adminUser) {
            echo "❌ Erro: Usuário 'admin@teste.com' não encontrado.\n";
            return;
        }

        $projects = [
            'Sistema de Gestão Acadêmica',
            'Plataforma de Estágios',
            'CRUD Virtualizado',
            'Críticas de Filmes',
            'Portal de Notícias Internas',
            'API de Clima',
        ];

        foreach ($projects as $title) {
            $project = new Project([
                'title'   => $title,
                'user_id' => $adminUser->id,
                'status'  => 'open' 
            ]);
            $project->save();
        }

        echo "✅ Projetos criados com sucesso!\n";
    }
}