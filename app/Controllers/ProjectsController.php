<?php

namespace App\Controllers;

use App\Models\Project;
use Core\Http\Request;
use Lib\Paginator;
use Core\Exceptions\HTTPException;

class ProjectsController extends BaseController
{
    private const PROJECTS_PER_PAGE = 5;

    public function index(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();
        $page = (int) ($params['page'] ?? 1);

        $paginator = new Paginator(
            Project::class,
            ['user_id' => $this->currentUser->getId()],
            self::PROJECTS_PER_PAGE,
            $page
        );

        $title = 'Projetos Cadastrados';
        $this->render('projects/index', compact('paginator', 'title'));
    }

    public function show(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();

        $project = $this->currentUser->projects()->find_by_id((int)$params['id']);
        if (!$project) {
            throw new HTTPException('Projeto não encontrado', 404);
        }

        $title = "Visualização do Projeto #{$project->getId()}";
        $this->render('projects/show', compact('project', 'title'));
    }

    public function new(Request $request): void
    {
        $this->authenticated();

        $project = $this->currentUser->projects()->new();
        $title = 'Novo Projeto';
        $this->render('projects/new', compact('project', 'title'));
    }

    public function create(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();

        $project = $this->currentUser->projects()->new();
        $project->setTitle($params['project']['title']);

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
        $params = $request->getParams();

        $project = $this->currentUser->projects()->find_by_id((int)$params['id']);
        if (!$project) {
            throw new HTTPException('Projeto não encontrado', 404);
        }

        $title = "Editar Projeto #{$project->getId()}";
        $this->render('projects/edit', compact('project', 'title'));
    }

    public function update(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();

        $project = $this->currentUser->projects()->find_by_id((int)$params['id']);
        if (!$project) {
            throw new HTTPException('Projeto não encontrado', 404);
        }

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
        $params = $request->getParams();

        $project = $this->currentUser->projects()->find_by_id((int)$params['id']);
        if ($project) {
            $project->destroy();
        }

        $this->redirectToRoute('projects.index');
    }
}