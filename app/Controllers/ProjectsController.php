<?php
namespace App\Controllers;
use App\Models\Project;

class ProjectsController
{
    private string $layout = 'application';

    public function index()
    {
        $projects = Project::all();
        $title = 'Projetos Cadastrados';
        $this->render('index', compact('projects', 'title'));
    }

    public function show()
    {
        $id = intval($_GET['id']);
        $project = Project::findById($id);
        $title = "Visualização do Projeto #{$id}";
        $this->render('show', compact('project', 'title'));
    }

    public function new()
    {
        $project = new Project();
        $title = 'Novo Projeto';
        $this->render('new', compact('project', 'title'));
    }

    public function create()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method !== 'POST') $this->redirectTo('/pages/projects');

        $params = $_POST['project'];
        $project = new Project($params['title']);

        if ($project->save()) {
            $this->redirectTo('/pages/projects');
        } else {
            $title = 'Novo Projeto';
            $this->render('new', compact('project', 'title'));
        }
    }

    public function edit()
    {
        $id = intval($_GET['id']);
        $project = Project::findById($id);
        $title = "Editar Projeto #{$id}";
        $this->render('edit', compact('project', 'title'));
    }

    public function update()
    {
        $method = $_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        if ($method !== 'PUT') $this->redirectTo('/pages/projects');

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

    public function destroy()
    {
        // 1. Pega o ID que vem da URL (via GET)
        $id = intval($_GET['id']);
        
        // 2. Encontra o projeto
        $project = Project::findById($id);

        // 3. Se o projeto existir, apaga
        if ($project) {
            $project->destroy();
        }

        // 4. Redireciona de volta para a lista
        $this->redirectTo('/pages/projects');
    }

    private function render($view, $data = [])
    {
        extract($data);
        // Caminho para as views de PROJETOS
        $view = '/var/www/app/views/projects/' . $view . '.phtml';
        require '/var/www/app/views/layouts/' . $this->layout . '.phtml';
    }

    private function redirectTo($location)
    {
        header('Location: ' . $location);
        exit;
    }
}