<?php

namespace App\Controllers;

use App\Models\Project;

class ProjectsController
{
    private string $layout = 'application';

    public function index(): void
    {
        $projects = Project::all();
        $title = 'Projetos Cadastrados';
        $this->render('index', compact('projects', 'title'));
    }

    public function show(): void
    {
        $id = intval($_GET['id']);
        $project = Project::findById($id);
        $title = "Visualização do Projeto #{$id}";
        $this->render('show', compact('project', 'title'));
    }

    public function new(): void
    {
        $project = new Project();
        $title = 'Novo Projeto';
        $this->render('new', compact('project', 'title'));
    }

    public function create(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method !== 'POST') {
            $this->redirectTo('/pages/projects');
        }

        $params = $_POST['project'];
        $project = new Project($params['title']);

        if ($project->save()) {
            $this->redirectTo('/pages/projects');
        } else {
            $title = 'Novo Projeto';
            $this->render('new', compact('project', 'title'));
        }
    }

    public function edit(): void
    {
        $id = intval($_GET['id']);
        $project = Project::findById($id);
        $title = "Editar Projeto #{$id}";
        $this->render('edit', compact('project', 'title'));
    }

    public function update(): void
    {
        $method = $_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        if ($method !== 'PUT') {
            $this->redirectTo('/pages/projects');
        }

        $params = $_POST['project'];
        $project = Project::findById($params['id']);
        $project->setTitle($params['title']);

        if ($project->save()) {
            $this->redirectTo('/pages/projects');
        } else {
            $title = "Editar Projeto #{$project->getId()}";
            $this->render('edit', compact('project', 'title'));
        }
    }

    public function destroy(): void
    {
        $id = intval($_GET['id']);

        $project = Project::findById($id);

        if ($project) {
            $project->destroy();
        }

        $this->redirectTo('/pages/projects');
    }

/**
 * @param array<string, mixed> $data
 */

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $view = '/var/www/app/views/projects/' . $view . '.phtml';
        require '/var/www/app/views/layouts/' . $this->layout . '.phtml';
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
