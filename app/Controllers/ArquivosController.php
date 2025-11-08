<?php

namespace App\Controllers;

use App\Models\Arquivo;
use App\Models\Project;
use App\Services\ArquivoService; 
use Core\Http\Request;
use Lib\FlashMessage;

class ArquivosController extends BaseController
{
    public function store(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();
        
        $params = $request->getParams();
        $project_id = (int)$params['id']; 

        $project = Project::findById($project_id);
        if (!$project) {
            FlashMessage::danger('Projeto não encontrado.');
            $this->redirectToRoute('projects.index');
            return;
        }

        if (!isset($_FILES['arquivo'])) {
            FlashMessage::danger('Nenhum arquivo enviado.');
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
            return;
        }
        $file = $_FILES['arquivo'];

        $service = new ArquivoService($project);
        
        $success = $service->upload($file);

        if ($success) {

            FlashMessage::success('Arquivo enviado com sucesso!');
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
        } else {
            
            $arquivos = $project->arquivos; 
            $title = "Visualização do Projeto #{$project->getId()}";
            
            $this->render('projects/show', compact('project', 'title', 'arquivos'));
        }
    }

    public function destroy(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();

        $params = $request->getParams();
        $arquivo_id = (int)$params['id']; 
        
        $arquivo = Arquivo::findById($arquivo_id);

        if ($arquivo) {
            $project_id = $arquivo->project_id; 
            
            $arquivo->deleteFileFromFilesystem();
            $arquivo->destroy(); 

            FlashMessage::success('Arquivo removido com sucesso.');
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
        } else {
            FlashMessage::danger('Arquivo não encontrado.');
            $this->redirectToRoute('projects.index'); 
        }
    }
}