<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveService
{
    protected Drive $drive;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(base_path(env('GOOGLE_DRIVE_CREDENTIALS')));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');

        // OAuth: gunakan refresh token
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');

        if (empty($refreshToken)) {
            throw new \RuntimeException(
                'GOOGLE_DRIVE_REFRESH_TOKEN belum diisi di .env. Dapatkan dari OAuth Playground.'
            );
        }

        $client->fetchAccessTokenWithRefreshToken($refreshToken);

        $this->drive = new Drive($client);
    }

    public function createFolder(string $name, ?string $parentId = null): string
    {
        $folderMetadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => $parentId ? [$parentId] : [env('GOOGLE_DRIVE_ROOT_FOLDER_ID')],
        ]);

        $folder = $this->drive->files->create($folderMetadata, [
            'fields' => 'id'
        ]);

        return $folder->id;
    }

    public function uploadFile($file, string $filename, string $folderId): string
    {
        $fileMetadata = new DriveFile([
            'name' => $filename,
            'parents' => [$folderId],
        ]);

        $content = file_get_contents($file->getRealPath());

        $uploadedFile = $this->drive->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        return $uploadedFile->id;
    }

    public function downloadFile(string $fileId): string
    {
        $response = $this->drive->files->get($fileId, [
            'alt' => 'media'
        ]);

        return $response->getBody()->getContents();
    }

    public function getFileMetadata(string $fileId)
    {
        return $this->drive->files->get($fileId, [
            'fields' => 'name, mimeType'
        ]);
    }

    public function deleteFile(string $fileId): void
    {
        $this->drive->files->delete($fileId);
    }


    public function createFolderIfNotExists(string $name, ?string $parentId = null): string
    {
        $parent = $parentId ?? env('GOOGLE_DRIVE_ROOT_FOLDER_ID');

        $files = $this->drive->files->listFiles([
            'q' => "name='{$name}' and '{$parent}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false",
            'fields' => 'files(id, name)'
        ]);

        if (count($files->files) > 0) {
            return $files->files[0]->id;
        }

        return $this->createFolder($name, $parent);
    }
}
