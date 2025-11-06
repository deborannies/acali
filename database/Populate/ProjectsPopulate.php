<?php

namespace Database\Populate;

use App\Models\Project;
use App\Models\User;

class ProjectsPopulate
{
    public static function populate(): void
    {
        $user = User::findByEmail('admin@teste.com');

        if (!$user) {
            echo "❌ Usuário 'admin@teste.com' não encontrado. Execute o UsersPopulate primeiro.\n";
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
            $project = $user->projects()->new();
            $project->setTitle($title);
            $project->save();
        }

        echo "✅ Projetos de teste criados para o usuário {$user->getEmail()} com sucesso!\n";
    }
}