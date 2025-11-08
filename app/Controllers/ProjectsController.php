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

        $paginator = Project::paginate($page, self::PROJECTS_PER_PAGE, 'projects.index');

        $title = 'Projetos Cadastrados';
        $this->render('projects/index', compact('paginator', 'title'));
    }

    public function show(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();
        
        $project = Project::findById((int)$params['id']); 

        if (!$project) {
            FlashMessage::danger('Projeto não encontrado.');
            $this->redirectToRoute('projects.index');
            return;
        }

        $arquivos = $project->arquivos; 
        
        $title = "Visualização do Projeto #{$project->id}"; 
        
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
        
        $project = new Project($params['project']);
        
        $project->user_id = $this->currentUser()->id; 
        
        if ($project->save()) { 
            FlashMessage::success('Projeto criado com sucesso!');
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
        $project = Project::findById((int)$params['id']);
        
        $title = "Editar Projeto #{$project->id}"; 

        $this->render('projects/edit', compact('project', 'title'));
    }

    public function update(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        
        $project = Project::findById((int)$params['id']);

        $project->title = $params['project']['title']; 

        if ($project->save()) {
            FlashMessage::success('Projeto atualizado com sucesso!');
            $this->redirectToRoute('projects.index');
        } else {
            $title = "Editar Projeto #{$project->id}"; 
            $this->render('projects/edit', compact('project', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        $params = $request->getParams();
        $project = Project::findById((int)$params['id']);
        
        if ($project) {
            $project->deleteAssociatedFiles();
            $project->destroy(); 
            
            FlashMessage::success('Projeto removido com sucesso.');
        }
        $this->redirectToRoute('projects.index');
    }
}