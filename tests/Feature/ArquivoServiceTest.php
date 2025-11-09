<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\Arquivo;
use App\Services\ArquivoService;
use Tests\TestCase;

class ArquivoServiceTest extends TestCase
{
    private ?User $user = null;
    private ?Project $project = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->mockRegularUser();

        $this->project = new Project([
            'title' => 'Projeto para Testar Uploads',
            'user_id' => $this->user->getId()
        ]);

        $this->assertTrue($this->project->save());
    }

    /** @test */
    public function it_can_upload_a_file_successfully(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tmpFile, 'Este é um teste de pdf falso.');

        $fakeFileArray = [
            'name' => 'teste.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tmpFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($tmpFile)
        ];

        $service = new ArquivoService($this->project);
        $result = $service->upload($fakeFileArray);

        $this->assertTrue($result);
        $this->assertFalse($this->project->hasErrors());

        $arquivo = Arquivo::findBy(['project_id' => $this->project->id]);
        $this->assertNotNull($arquivo);
        $this->assertEquals('teste.pdf', $arquivo->nome_original);

        $fullPath = __DIR__ . '/../../public/' . $arquivo->path_arquivo;
        $this->assertFileExists($fullPath);

        unlink($fullPath);
    }

    /** @test */
    public function it_can_delete_a_file(): void
    {
        $fakeUploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($fakeUploadDir)) {
            mkdir($fakeUploadDir, 0775, true);
        }
        $fakeFilePath = 'uploads/fake_file_to_delete.txt';
        $fullPath = $fakeUploadDir . 'fake_file_to_delete.txt';
        file_put_contents($fullPath, 'delete me');

        $arquivo = new Arquivo([
            'project_id' => $this->project->id,
            'path_arquivo' => $fakeFilePath,
            'nome_original' => 'fake_file_to_delete.txt',
            'mime_type' => 'text/plain'
        ]);
        $this->assertTrue($arquivo->save());
        $this->assertFileExists($fullPath);
        $this->assertNotNull(Arquivo::findById($arquivo->id));

        $arquivo->deleteFileFromFilesystem();
        $arquivo->destroy();

        $this->assertFileDoesNotExist($fullPath);
        $this->assertNull(Arquivo::findById($arquivo->id));
    }
}
