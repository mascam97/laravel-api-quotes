<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $_ENV['APP_ENV'] = 'testing';
        $_SERVER['APP_ENV'] = 'testing';
        putenv('APP_ENV=testing');

        $_ENV['APP_KEY'] ??= 'base64:9n2Q9nZzDg6t4z9rYxGm4bQkQ8kJ3xWm7cT5pL2sV1A=';
        $_SERVER['APP_KEY'] = $_ENV['APP_KEY'];
        putenv('APP_KEY='.$_ENV['APP_KEY']);

        $privateKeyPath = dirname(__DIR__).'/storage/oauth-private.key';
        $publicKeyPath = dirname(__DIR__).'/storage/oauth-public.key';

        if (! file_exists($privateKeyPath) || ! file_exists($publicKeyPath)) {
            $key = openssl_pkey_new([
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]);

            if ($key === false || ! openssl_pkey_export($key, $privateKey)) {
                throw new \RuntimeException('Unable to generate Passport test keys.');
            }

            $details = openssl_pkey_get_details($key);

            if ($details === false || ! isset($details['key'])) {
                throw new \RuntimeException('Unable to export the Passport public test key.');
            }

            file_put_contents($privateKeyPath, $privateKey);
            file_put_contents($publicKeyPath, $details['key']);
            chmod($privateKeyPath, 0600);
            chmod($publicKeyPath, 0640);
        }

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
