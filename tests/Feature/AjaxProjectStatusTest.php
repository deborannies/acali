<?php

namespace Tests\Unit\Feature;

use App\Controllers\ProjectsController;
use App\Models\Project;
use App\Models\User;
use Core\Http\Request;
use Tests\TestCase;

class AjaxProjectStatusTest extends TestCase
{
    private User $user;
    private Project $project;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user = new User([
            'name' => 'Tester Ajax',
            'email' => 'ajax@tester.com',
            'encrypted_password' => '123456',
            'role' => 'admin'
        ]);
        $this->user->save();

        $this->project = new Project([
            'title' => 'Projeto Ajax',
            'user_id' => $this->user->id,
            'status'  => 'open'
        ]);
        $this->project->save();
    }

    public function test_it_toggles_status_via_ajax_controller(): void
    {
        $_SESSION['user']['id'] = $this->user->id;

        $request = new Request(['id' => $this->project->id]);
        $controller = new ProjectsController();

        ob_start();
        try {
            $controller->toggleStatus($request);
        } catch (\Exception $e) {
        }
        $output = ob_get_clean();

        $updatedProject = Project::findById($this->project->id);
        $this->assertEquals('finished', $updatedProject->status);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertEquals('finished', $response['new_status']);
        
        ob_start();
        try {
            $controller->toggleStatus($request);
        } catch (\Exception $e) {}
        $output = ob_get_clean();
        
        $updatedProject = Project::findById($this->project->id);
        $this->assertEquals('open', $updatedProject->status);
    }

    public function test_unauthenticated_user_cannot_access_ajax_route(): void
    {
        unset($_SESSION['user']);

        $request = new Request(['id' => $this->project->id]);
        $controller = new ProjectsController();

        ob_start();
        try {
            $controller->toggleStatus($request);
        } catch (\Exception $e) {
        }
        $output = ob_get_clean();

        $projectCheck = Project::findById($this->project->id);
        $this->assertEquals('open', $projectCheck->status);

        $this->assertNotEmpty($_SESSION['flash_message'] ?? []);
    }
}