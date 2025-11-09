<?php

namespace App\Models;

use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use App\Models\Arquivo;
use App\Models\User;

/**
 * @property string $title
 * @property int $user_id
 * @property-read array<Arquivo> $arquivos
 * @property-read User $user
 */
class Project extends Model
{
    protected static string $table = 'projects';
    protected static array $columns = ['title', 'user_id'];

    public function validates(): void
    {
        if (empty($this->title)) {
            $this->addError('title', 'não pode ser vazio!');
        }
        if (empty($this->user_id)) {
             $this->addError('user_id', 'deve ser atribuído a um usuário!');
        }
    }

    public function arquivos(): HasMany
    {
        return $this->hasMany(Arquivo::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deleteAssociatedFiles(): void
    {
        $arquivos = $this->arquivos;

        if (is_array($arquivos)) {
            foreach ($arquivos as $arquivo) {
                $arquivo->deleteFileFromFilesystem();
            }
        }
    }
}
