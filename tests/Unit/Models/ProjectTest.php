<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use Tests\TestCase; // Alterado

class ProjectTest extends TestCase // Alterado
{
    public function test_can_set_title()
    {
        $project = new Project(title: 'Projeto ACALI Teste');
        $this->assertEquals('Projeto ACALI Teste', $project->getTitle());
    }

    public function test_should_create_new_project()
    {
        $project = new Project(title: 'Novo Projeto de Pesquisa');
        $this->assertTrue($project->save());
        $this->assertCount(1, Project::all());
    }
}