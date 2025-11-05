<?php

namespace App\Models;

use Core\Database\Database;
use PDO;

class Arquivo
{
    // Define o caminho para a pasta 'public'
    // __DIR__ é 'app/Models', ../../ é a raiz 'acali/'
    private const PUBLIC_PATH = __DIR__ . '/../../public';

    public function __construct(
        private int $project_id,
        private string $path_arquivo,
        private string $nome_original,
        private string $mime_type,
        private int $id = -1
    ) {
    }

    // --- Getters ---
    public function getId(): int
    {
        return $this->id;
    }
    public function getProjectId(): int
    {
        return $this->project_id;
    }
    public function getPathArquivo(): string
    {
        return $this->path_arquivo;
    }
    public function getNomeOriginal(): string
    {
        return $this->nome_original;
    }

    /**
     * Retorna o caminho público para o link (ex: /uploads/arquivo.pdf)
     */
    public function getPublicPath(): string
    {
        // Garante que o caminho comece com /
        return '/' . ltrim($this->path_arquivo, '/');
    }

    // --- Lógica de Banco ---

    public function save(): bool
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'INSERT INTO arquivos (project_id, path_arquivo, nome_original, mime_type) 
                VALUES (:project_id, :path_arquivo, :nome_original, :mime_type);';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':project_id', $this->project_id, PDO::PARAM_INT);
        $stmt->bindParam(':path_arquivo', $this->path_arquivo);
        $stmt->bindParam(':nome_original', $this->nome_original);
        $stmt->bindParam(':mime_type', $this->mime_type);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $this->id = (int) $pdo->lastInsertId();
            return true;
        }
        return false;
    }

    public function destroy(): bool
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'DELETE FROM arquivos WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return ($stmt->rowCount() !== 0);
    }

    /**
     * Apaga o arquivo do DISCO (Filesystem) - (Item 4.2)
     */
    public function deleteFileFromFilesystem(): bool
    {
        $fullPath = self::PUBLIC_PATH . '/' . $this->path_arquivo;
        if (file_exists($fullPath)) {
            return unlink($fullPath); // Deleta o arquivo
        }
        return false;
    }

    // --- Métodos Estáticos ---

    public static function findById(int $id): ?Arquivo
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT * FROM arquivos WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Arquivo(
            id: $row['id'],
            project_id: $row['project_id'],
            path_arquivo: $row['path_arquivo'],
            nome_original: $row['nome_original'],
            mime_type: $row['mime_type']
        );
    }

    /**
     * Busca todos os arquivos de um projeto específico (Relação 1xN)
     * @return array<int, Arquivo>
     */
    public static function findByProjectId(int $projectId): array
    {
        $arquivos = [];
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT * FROM arquivos WHERE project_id = :project_id ORDER BY created_at DESC;';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $arquivos[] = new Arquivo(
                id: $row['id'],
                project_id: $row['project_id'],
                path_arquivo: $row['path_arquivo'],
                nome_original: $row['nome_original'],
                mime_type: $row['mime_type']
            );
        }
        return $arquivos;
    }
}
