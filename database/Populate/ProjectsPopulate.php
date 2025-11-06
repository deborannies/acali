<?php

namespace Database\Populate;

use App\Models\Project;

class ProjectsPopulate
{
    public static function populate(): void
    {
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
            $project = new Project(title: $title);
            $project->save();
        }

        echo "✅ Projetos de teste criados com sucesso!\n";
    }
}