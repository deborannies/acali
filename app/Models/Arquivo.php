<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use App\Models\Project;

class Arquivo extends Model
{
    private const PUBLIC_PATH = __DIR__ . '/../../public';

    protected static string $table = 'arquivos';

    protected static array $columns = [
        'project_id',
        'path_arquivo',
        'nome_original',
        'mime_type'
    ];

    public function getPublicPath(): string
    {
        return '/' . ltrim($this->path_arquivo, '/');
    }

    public function deleteFileFromFilesystem(): bool
    {
        $fullPath = self::PUBLIC_PATH . '/' . $this->path_arquivo;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}