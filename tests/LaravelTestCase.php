<?php

namespace MBLSolutions\InspiredDeckLaravel\Tests;

use Illuminate\Foundation\Application;
use MBLSolutions\Report\Tests\Fakes\ExportDriver\FakeExportDriver;
use MBLSolutions\Report\Tests\Fakes\Middleware\FakeMiddleware;

class LaravelTestCase extends \Orchestra\Testbench\TestCase
{

    /** {@inheritdoc} **/
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupEnvVariables();
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $config = include __DIR__ . '/../config/inspireddeck.php';

        $app['config']->set('inspireddeck.session', 'inspireddeck_auth_session');
        $app['config']->set('inspireddeck.client_id', 1);
        $app['config']->set('inspireddeck.secret', 'secret');
        $app['config']->set('inspireddeck.roles', [
            'admin',
            'programme_manager',
            'customer_service_manager',
            'customer_service_operator',
            //'store_manager',
            //'store_operator',
            //'report',
        ]);
    }


    /**
     * Setup environment variables
     *
     * @return void
     */
    private function setupEnvVariables(): void
    {
        $this->app['config']->set('app.key', 'base64:KMRokGdMt+pgOmbRD+oiKwmfZiKAVxR6KkZ4KuiIo90=');
    }

}