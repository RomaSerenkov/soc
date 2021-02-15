<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $targetDir;

    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(string $path, UploadedFile $file): string
    {
        $fileName = "{$this->generateUniqueFileName()}.{$file->guessExtension()}";
        $filePath = "{$this->getTargetDir()}/{$path}";

        try {
            $file->move($filePath, $fileName);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

        return $fileName;
    }

    public function delete(string $path): bool
    {
        $filePath = "{$this->getTargetDir()}/{$path}";

        if (file_exists($filePath)) {
            unlink($filePath);

            return true;
        }

        return false;
    }

    public function getTargetDir(): string
    {
        return $this->targetDir;
    }

    private function generateUniqueFileName(): string
    {
        return md5(uniqid());
    }
}
