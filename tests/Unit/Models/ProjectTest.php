<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\User;
use App\Models\Arquivo;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    public function test_it_can_set_properties_using_the_constructor_and_magic_get(): void
    {
        $project = new Project(['title' => 'Projeto ACALI Teste', 'user_id' => 1]);
        $this->assertEquals('Projeto ACALI Teste', $project->title);
        $this->assertEquals(1, $project->user_id);
    }

    public function test_it_is_invalid_without_a_title(): void
    {
        $project = new Project(['user_id' => 1]);
        $this->assertFalse($project->isValid());
        $this->assertNotNull($project->errors('title'));
    }

    public function test_it_is_invalid_without_a_user_id(): void
    {
        $project = new Project(['title' => 'Projeto de Teste']);
        $this->assertFalse($project->isValid());
        $this->assertNotNull($project->errors('user_id'));
    }

    public function test_it_can_be_saved_to_the_database(): void
    {
        $user = $this->mockRegularUser();

        $project = new Project([
            'title' => 'Novo Projeto de Pesquisa',
            'user_id' => $user->getId()
        ]);

        $this->assertTrue($project->save());
        $this->assertNotNull($project->id);

        $foundProject = Project::findById($project->id);
        $this->assertEquals('Novo Projeto de Pesquisa', $foundProject->title);
    }

    public function test_deleting_a_project_also_deletes_its_arquivos_from_database(): void
    {
        $user = $this->mockRegularUser();

        $project = new Project(['title' => 'Cascade Project', 'user_id' => $user->getId()]);
        $this->assertTrue($project->save());
        $arquivo1 = new Arquivo([
            'project_id' => $project->id,
            'path_arquivo' => 'f1.pdf',
            'nome_original' => 'f1.pdf',
            'mime_type' => 'app/pdf'
        ]);
        $this->assertTrue($arquivo1->save());

        $arquivo2 = new Arquivo([
            'project_id' => $project->id,
            'path_arquivo' => 'f2.pdf',
            'nome_original' => 'f2.pdf',
            'mime_type' => 'app/pdf'
        ]);
        $this->assertTrue($arquivo2->save());

        $this->assertEquals(2, count($project->arquivos));
        $this->assertNotNull(Arquivo::findById($arquivo1->id));

        $project->deleteAssociatedFiles();
        $project->destroy();

        $this->assertNull(Project::findById($project->id));
        $this->assertNull(Arquivo::findById($arquivo1->id));
        $this->assertNull(Arquivo::findById($arquivo2->id));
    }

    public function test_it_can_attach_user_to_team(): void
    {
        $user = \App\Models\User::findBy(['email' => 'user@teste.com']);

        if (!$user) {
             $this->markTestSkipped();
        }

        $project = new \App\Models\Project([
            'title' => 'Projeto NxN',
            'user_id' => $user->id
        ]);
        $project->save();

        $project->team()->attach($user->id);

        $this->assertEquals(1, $project->team()->count());

        $membros = $project->team()->get();
        $this->assertEquals($user->id, $membros[0]->id);
    }

    public function test_it_can_detach_user_from_team(): void
    {
        $user = \App\Models\User::findBy(['email' => 'user@teste.com']);

        if (!$user) {
             $this->markTestSkipped();
        }

        $project = new \App\Models\Project(['title' => 'Projeto Detach', 'user_id' => $user->id]);
        $project->save();

        $project->team()->attach($user->id);

        $this->assertEquals(1, $project->team()->count());

        $project->team()->detach($user->id);

        $this->assertEquals(0, $project->team()->count());
    }
}
