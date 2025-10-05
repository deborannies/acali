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
        ];

        foreach ($projects as $title) {
            $project = new Project();
            $project->setTitle($title);
            $project->save();
        }

        echo "✅ Projetos de teste criados com sucesso!\n";
    }
}
