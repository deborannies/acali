<?php

namespace Tests\Unit\Controllers;

use App\Controllers\ProjectsController;
use App\Models\Project;

class ProjectsControllerTest extends ControllerTestCase
{
    public function test_list_all_projects()
    {
        $project1 = new Project(title: 'Projeto TCC ACALI');
        $project2 = new Project(title: 'Pesquisa sobre IA');
        $project1->save();
        $project2->save();

        $response = $this->get(action: 'index', controller: ProjectsController::class);


        $this->assertMatchesRegularExpression("/{$project1->getTitle()}/", $response);
        $this->assertMatchesRegularExpression("/{$project2->getTitle()}/", $response);
    }
}
