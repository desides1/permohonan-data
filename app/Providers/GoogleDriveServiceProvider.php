<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Client;
use Google\Service\Drive;
use League\Flysystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('google', function (Application $app, array $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $token = $client->getAccessToken();

            if (empty($token) || isset($token['error'])) {
                throw new \RuntimeException('Failed to authenticate with Google Drive: ' . ($token['error'] ?? 'Unknown error'));
            }

            $service = new Drive($client);

            $folderId = $config['folder'] ?? null;

            \Log::info('Google Drive folderId:', ['folderId' => $folderId]);

            $adapter = new GoogleDriveAdapter(
                $service,
                $folderId,
                ['useHasDir' => true]
            );

            $driver = new Filesystem($adapter, [
                'visibility' => 'public',
            ]);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}
