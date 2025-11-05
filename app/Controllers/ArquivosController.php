<?php

namespace App\Controllers;

use App\Models\Arquivo;
use Core\Http\Request;
use Lib\FlashMessage;

class ArquivosController extends BaseController
{
    private const PUBLIC_PATH = __DIR__ . '/../../public';

    /**
     * Processa o upload do arquivo (Item 3.2, 4.1, 5)
     */
    public function store(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();

        $params = $request->getParams();
        $project_id = (int)$params['id'];

        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            FlashMessage::danger('Erro no upload: Nenhum arquivo enviado ou erro no envio.');
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
            return;
        }
        $file = $_FILES['arquivo'];

        if (!$this->validateFile($file)) {
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
            return;
        }

        $uploadDir = 'uploads/';
        $nomeOriginal = basename($file['name']);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $novoNome = 'proj_' . $project_id . '_' . uniqid() . '.' . $extensao;
        $destinationPathRelativo = $uploadDir . $novoNome;
        $destinationPathAbsoluto = self::PUBLIC_PATH . '/' . $destinationPathRelativo;

        if (move_uploaded_file($file['tmp_name'], $destinationPathAbsoluto)) {
            $arquivo = new Arquivo(
                project_id: $project_id,
                path_arquivo: $destinationPathRelativo,
                nome_original: $nomeOriginal,
                mime_type: $file['type']
            );

            require_once __DIR__ . '/../Models/Arquivo.php';
            $arquivo->save();
            FlashMessage::success('Arquivo enviado com sucesso!');
        } else {
            FlashMessage::danger('Erro ao mover o arquivo para o destino.');
        }

        $this->redirectToRoute('projects.show', ['id' => $project_id]);
    }

    /**
     * Remove um arquivo (Item 3.3, 4.2)
     */
    public function destroy(Request $request): void
    {
        $this->authenticated();
        $this->adminOnly();

        $params = $request->getParams();
        $arquivo_id = (int)$params['id'];

        require_once __DIR__ . '/../Models/Arquivo.php';
        $arquivo = Arquivo::findById($arquivo_id);

        if ($arquivo) {
            $project_id = $arquivo->getProjectId();
            $arquivo->deleteFileFromFilesystem();
            $arquivo->destroy();
            FlashMessage::success('Arquivo removido com sucesso.');
            $this->redirectToRoute('projects.show', ['id' => $project_id]);
        } else {
            FlashMessage::danger('Arquivo não encontrado.');
            $this->redirectToRoute('projects.index');
        }
    }

    /**
     * @param array{name: string, type: string, tmp_name: string, error: int, size: int} $file
     */
    private function validateFile(array $file): bool
    {
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            FlashMessage::danger('Arquivo muito grande (Max 2MB).');
            return false;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            FlashMessage::danger("Tipo de arquivo não permitido: {$mimeType}. (Permitidos: JPEG, PNG, PDF)");
            return false;
        }

        return true;
    }
}
