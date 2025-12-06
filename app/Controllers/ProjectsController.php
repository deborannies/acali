<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Arquivo;
use App\Models\User;
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
        $teamMembers = $project->team()->get();
        $allUsers = User::all();

        $title = "Visualização do Projeto #{$project->id}";

        $this->render('projects/show', compact('project', 'title', 'arquivos', 'teamMembers', 'allUsers'));
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

    public function addMember(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();

        $projectId = (int)$params['id'];
        $userIdToAdd = (int)$params['user_id'];

        $project = Project::findById($projectId);

        if ($project && $userIdToAdd) {
            try {
                $project->team()->attach($userIdToAdd);
                FlashMessage::success('Membro adicionado à equipe!');
            } catch (\Exception $e) {
                FlashMessage::danger('Erro: Este usuário já faz parte da equipe.');
            }
        }

        $this->redirectToRoute('projects.show', ['id' => $projectId]);
    }

    public function removeMember(Request $request): void
    {
        $this->authenticated();
        $params = $request->getParams();

        $projectId = (int)$params['id'];
        $userIdToRemove = (int)$params['user_id'];

        $project = Project::findById($projectId);

        if ($project && $userIdToRemove) {
            $project->team()->detach($userIdToRemove);
            FlashMessage::success('Membro removido da equipe.');
        }

        $this->redirectToRoute('projects.show', ['id' => $projectId]);
    }

    public function toggleStatus(Request $request): void
    {
        $this->authenticated();

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $params = $request->getParams();
        $id = (int)$params['id'];

        $project = Project::findById($id);

        if (!$project) {
            $this->renderJson(['success' => false, 'message' => 'Projeto não encontrado'], 404);
            return;
        }

        $newStatus = ($project->status === 'finished') ? 'open' : 'finished';
        $project->status = $newStatus;

        if ($project->save()) {
            $this->renderJson([
                'success' => true,
                'new_status' => $newStatus,
                'label' => ($newStatus === 'finished') ? 'Concluído' : 'Em Andamento',
                'message' => 'Status atualizado com sucesso!'
            ]);
        } else {
            $this->renderJson(['success' => false, 'message' => 'Erro ao salvar no banco'], 500);
        }
    }
}
