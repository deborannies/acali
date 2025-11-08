<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\User;
use App\Models\Arquivo;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    /** @test */
    public function it_can_set_properties_using_the_constructor_and_magic_get()
    {
        $project = new Project(['title' => 'Projeto ACALI Teste', 'user_id' => 1]);
        $this->assertEquals('Projeto ACALI Teste', $project->title);
        $this->assertEquals(1, $project->user_id);
    }

    /** @test */
    public function it_is_invalid_without_a_title()
    {
        $project = new Project(['user_id' => 1]);
        $this->assertFalse($project->isValid());
        $this->assertNotNull($project->errors('title'));
    }

    /** @test */
    public function it_is_invalid_without_a_user_id()
    {
        $project = new Project(['title' => 'Projeto de Teste']);
        $this->assertFalse($project->isValid());
        $this->assertNotNull($project->errors('user_id'));
    }

    /** @test */
    public function it_can_be_saved_to_the_database()
    {
        $user = User::find(1);
        if (!$user) {
            $user = new User(['name' => 'Test User', 'email' => 'test@user.com', 'password' => '123', 'role' => 'user']);
            $user->save();
        }

        $project = new Project([
            'title' => 'Novo Projeto de Pesquisa',
            'user_id' => $user->id
        ]);
        
        $this->assertTrue($project->save());
        $this->assertNotNull($project->id);

        $foundProject = Project::findById($project->id);
        $this->assertEquals('Novo Projeto de Pesquisa', $foundProject->title);
    }

    /** @test */
    public function deleting_a_project_also_deletes_its_arquivos_from_database()
    {
        $user = new User(['name' => 'Cascade Test', 'email' => 'cascade@test.com', 'password' => '123', 'role' => 'user']);
        $user->save();
        
        $project = new Project(['title' => 'Cascade Project', 'user_id' => $user->id]);
        $project->save();

        $arquivo1 = new Arquivo(['project_id' => $project->id, 'path_arquivo' => 'f1.pdf', 'nome_original' => 'f1.pdf', 'mime_type' => 'app/pdf']);
        $arquivo1->save();
        $arquivo2 = new Arquivo(['project_id' => $project->id, 'path_arquivo' => 'f2.pdf', 'nome_original' => 'f2.pdf', 'mime_type' => 'app/pdf']);
        $arquivo2->save();

        $this->assertEquals(2, count($project->arquivos));
        $this->assertNotNull(Arquivo::findById($arquivo1->id));

        $project->deleteAssociatedFiles();
        $project->destroy();

        $this->assertNull(Project::findById($project->id));
        $this->assertNull(Arquivo::findById($arquivo1->id));
        $this->assertNull(Arquivo::findById($arquivo2->id));
    }
}