<?php

namespace App\Services;

use App\Models\Arquivo;
use App\Models\Project;

class ArquivoService
{
    private const PUBLIC_PATH = __DIR__ . '/../../public';
    private const UPLOAD_DIR = 'uploads/';
    private const MAX_SIZE = 2 * 1024 * 1024;
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'application/pdf'];

    public function __construct(
        private Project $project
    ) {
    }

    /**
     * @param array<string, mixed> $file
     */
    public function upload(array $file): bool
    {
        if (!$this->validateFile($file)) {
            return false;
        }

        $nomeOriginal = basename($file['name']);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $novoNome = 'proj_' . $this->project->id . '_' . uniqid() . '.' . $extensao;

        $destinationPathRelativo = self::UPLOAD_DIR . $novoNome;
        $destinationPathAbsoluto = self::PUBLIC_PATH . '/' . $destinationPathRelativo;

        $moveSuccess = false;
        if (PHP_SAPI === 'cli' || defined('PHPUNIT_RUNNING')) {
            $moveSuccess = rename($file['tmp_name'], $destinationPathAbsoluto);
        } else {
            $moveSuccess = move_uploaded_file($file['tmp_name'], $destinationPathAbsoluto);
        }

        if (!$moveSuccess) {
            $this->project->addError('arquivo', 'Erro ao mover o ficheiro para o destino.');
            return false;
        }

        $arquivo = new Arquivo([
            'project_id' => $this->project->id,
            'path_arquivo' => $destinationPathRelativo,
            'nome_original' => $nomeOriginal,
            'mime_type' => $file['type']
        ]);

        if ($arquivo->save()) {
            return true;
        } else {
            unlink($destinationPathAbsoluto);
            $this->project->addError('arquivo', 'Erro ao salvar informações na base de dados.');
            return false;
        }
    }

    /**
     * @param array<string, mixed> $file
     */
    private function validateFile(array $file): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->project->addError('arquivo', 'Nenhum ficheiro enviado ou erro no envio.');
            return false;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->project->addError('arquivo', 'Arquivo muito grande (Max 2MB).');
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (PHP_SAPI === 'cli' && $mimeType !== $file['type']) {
            $mimeType = $file['type'];
        }

        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            $this->project->addError('arquivo', "Tipo de ficheiro não permitido: {$mimeType}.");
            return false;
        }

        return true;
    }
}
