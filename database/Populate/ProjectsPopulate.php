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
            echo "❌ Erro: Usuário 'admin@teste.com' não encontrado para associar projetos.\n";
            return;
        }

        $projects = [
            'Sistema de Gestão Acadêmica',
            'Plataforma de Estágios',
            'CRUD Virtualizado',
            'Críticas de Filmes',
            'Portal de Notícias Internas',
            'Sistema de Biblioteca',
            'Ferramenta de E-commerce',
            'Blog de Tecnologia',
            'Gerenciador de Tarefas (Kanban)',
            'Sistema de Votação Online',
            'API de Clima',
        ];

        foreach ($projects as $title) {
            $project = new Project([
                'title' => $title,
                'user_id' => $adminUser->id
            ]);
            $project->save();
        }

        echo "✅ Projetos de teste criados com sucesso!\n";
    }
}