<?php

namespace Database\Populate;

use App\Models\Project;
use App\Models\User;
use Core\Database\Database;

class ProjectUserPopulate
{
    public static function populate(): void
    {
        $pdo = Database::getDatabaseConn();
        
        $pdo->exec("DELETE FROM project_user");
        echo "🔄 Tabela intermediária 'project_user' limpa.\n";

        $projects = Project::all();
        $users = User::all();

        if (empty($projects) || empty($users)) {
            echo "❌ Erro: Não há projetos ou usuários suficientes para criar a relação.\n";
            return;
        }

        $count = 0;

        foreach ($projects as $project) {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            try {
                $project->team()->attach($user->id);
                $count++;
            } catch (\Exception $e) {
            }
        }

        echo "✅ Equipes montadas! {$count} associações foram criadas entre Projetos e Usuários.\n";
    }
}