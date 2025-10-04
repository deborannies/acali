<?php

namespace App\Controllers;

use App\Models\Project;
use Core\Http\Request;

class ProjectsController
{
    private string $layout = 'application';

    public function index(Request $request): void
    {
        $projects = Project::all();
        $title = 'Projetos Cadastrados';
        $this->render('index', compact('projects', 'title'));
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $title = "Visualização do Projeto #{$project->getId()}";
        $this->render('show', compact('project', 'title'));
    }

    public function new(Request $request): void
    {
        $project = new Project();
        $title = 'Novo Projeto';
        $this->render('new', compact('project', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParams();
        $project = new Project($params['project']['title']);

        if ($project->save()) {
            $this->redirectToRoute('projects.index');
        } else {
            $title = 'Novo Projeto';
            $this->render('new', compact('project', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $title = "Editar Projeto #{$project->getId()}";
        $this->render('edit', compact('project', 'title'));
    }

    public function update(Request $request): void
    {
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $project->setTitle($params['project']['title']);

        if ($project->save()) {
            $this->redirectToRoute('projects.index');
        } else {
            $title = "Editar Projeto #{$project->getId()}";
            $this->render('edit', compact('project', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();
        $project = Project::findById($params['id']);

        if ($project) {
            $project->destroy();
        }

        $this->redirectToRoute('projects.index');
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
    
    /**
     * @param array<string, mixed> $params
     */
    private function redirectToRoute(string $routeName, array $params = []): void
    {
        $location = route($routeName, $params);
        header('Location: ' . $location);
        exit;
    }
}