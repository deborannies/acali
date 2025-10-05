<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\User;
use Core\Http\Request;
use Lib\FlashMessage;

class ProjectsController extends BaseController
{
    protected ?User $currentUser = null;

    private function currentUser(): ?User
    {
        if ($this->currentUser === null && isset($_SESSION['user']['id'])) {
            $this->currentUser = User::find($_SESSION['user']['id']);
        }
        return $this->currentUser;
    }

    private function authenticated(): void
    {
        if ($this->currentUser() === null) {
            FlashMessage::danger('Você deve estar logado para acessar essa página.');
            $this->redirectToRoute('login.form');
            exit;
        }
    }

    private function isAdmin(): bool
    {
        $user = $this->currentUser();
        return $user && $user->getRole() === 'admin';
    }

    private function adminOnly(): void
    {
        if (!$this->isAdmin()) {
            FlashMessage::danger('Você não tem permissão para acessar essa página.');
            $this->redirectToRoute('projects.index');
            exit;
        }
    }

    public function index(Request $request): void
    {
        $this->authenticated();
        $projects = Project::all();
        $title = 'Projetos Cadastrados';
        $this->render('projects/index', compact('projects', 'title'));
    }

    public function show(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $title = "Visualização do Projeto #{$project->getId()}";
        $this->render('projects/show', compact('project', 'title'));
    }

    public function new(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $project = new Project();
        $title = 'Novo Projeto';
        $this->render('projects/new', compact('project', 'title'));
    }

    public function create(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        $project = new Project($params['project']['title']);
        if ($project->save()) {
            $this->redirectToRoute('projects.index');
        } else {
            $title = 'Novo Projeto';
            $this->render('projects/new', compact('project', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $title = "Editar Projeto #{$project->getId()}";
        $this->render('projects/edit', compact('project', 'title'));
    }

    public function update(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        $project->setTitle($params['project']['title']);
        if ($project->save()) {
            $this->redirectToRoute('projects.index');
        } else {
            $title = "Editar Projeto #{$project->getId()}";
            $this->render('projects/edit', compact('project', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        $project = Project::findById($params['id']);
        if ($project) {
            $project->destroy();
        }
        $this->redirectToRoute('projects.index');
    }
}
