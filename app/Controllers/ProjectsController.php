<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Arquivo;
use Core\Http\Request;
use Lib\Paginator;
use Lib\FlashMessage;

class ProjectsController extends BaseController
{
    private const PROJECTS_PER_PAGE = 5;

    public function index(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();
        $page = (int) ($params['page'] ?? 1);
        $limit = self::PROJECTS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $totalProjects = Project::countAll();
        $projects = Project::all($limit, $offset);

        $paginator = new Paginator($projects, $totalProjects, $limit, $page);

        $title = 'Projetos Cadastrados';
        $this->render('projects/index', compact('paginator', 'title'));
    }

    public function show(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();
        $project = Project::findById($params['id']);

        if (!$project) {
            FlashMessage::danger('Projeto não encontrado.');
            $this->redirectToRoute('projects.index');
            return;
        }

        $arquivos = $project->getArquivos();
        $title = "Visualização do Projeto #{$project->getId()}";
        $this->render('projects/show', compact('project', 'title', 'arquivos'));
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
            $arquivos = $project->getArquivos();
            foreach ($arquivos as $arquivo) {
                $arquivo->deleteFileFromFilesystem();
            }
            $project->destroy();

            FlashMessage::success('Projeto removido com sucesso.');
        }
        $this->redirectToRoute('projects.index');
    }
}
