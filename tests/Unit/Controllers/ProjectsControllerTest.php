<?php

namespace Tests\Unit\Controllers;

use App\Controllers\ProjectsController;
use App\Models\Project;
use App\Models\User;

class ProjectsControllerTest extends ControllerTestCase
{
    public function test_list_all_projects(): void
    {
        $user = $this->mockRegularUser();

        $this->actingAs($user);

        $project1 = new Project([
            'title' => 'Projeto TCC ACALI',
            'user_id' => $user->getId()
        ]);
        $project2 = new Project([
            'title' => 'Pesquisa sobre IA',
            'user_id' => $user->getId()
        ]);

        $this->assertTrue($project1->save());
        $this->assertTrue($project2->save());

        $response = $this->get(action: 'index', controller: ProjectsController::class);

        $this->assertMatchesRegularExpression("/{$project1->title}/", $response);
        $this->assertMatchesRegularExpression("/{$project2->title}/", $response);
    }
}
