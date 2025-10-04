<?php

namespace Database\Populate;

use App\Models\Project;

class ProjectsPopulate
{
    public static function populate()
    {
        $numberOfProjects = 100;

        for ($i = 1; $i <= $numberOfProjects; $i++) {
            $project = new Project(title: 'Project ' . $i);
            $project->save();
        }

        echo "Projects populated with $numberOfProjects registers\n";
    }
}