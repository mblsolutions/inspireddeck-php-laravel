<?php

namespace MBLSolutions\InspiredDeckLaravel;

use Throwable;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;
use MBLSolutions\InspiredDeck\Exceptions\NotFoundException;
use MBLSolutions\InspiredDeck\Exceptions\PermissionDeniedException;
use MBLSolutions\InspiredDeck\Exceptions\ValidationException;
use MBLSolutions\InspiredDeck\InspiredDeck;
use MBLSolutions\InspiredDeckLaravel\Middleware\LoadInspiredDeckConfig;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InspiredDeckServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/inspireddeck.php' => config_path('inspireddeck.php'),
        ], 'config');

        $this->registerMiddleware(LoadInspiredDeckConfig::class);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InspiredDeck::class, static function () {
            InspiredDeck::setBaseUri(config('inspireddeck.endpoint'));
            InspiredDeck::setVerifySSL(config('inspireddeck.verify_ssl', true));

            return new InspiredDeck;
        });
    }

    /**
     * Register Middleware
     *
     * @param $middleware
     * @return void
     */
    public function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];

        $kernel->pushMiddleware($middleware);
    }

    /**
     * Inspired Deck Exception Handling
     *
     * @param $request
     * @param Throwable $exception
     * @param callable|null $function
     * @return JsonResponse|RedirectResponse
     */
    public static function exceptionHandling($request, Throwable $exception, callable $function = null)
    {
        if (route_contains('async') || route_contains('api')) {
            if ($exception instanceof ValidationException) {
                return JsonResponse::create([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getValidationErrors()
                ], $exception->getCode());
            }
        }

        if ($exception instanceof HttpException) {
            if ($exception->getStatusCode() === 401) {
                return redirect()->route('login')->withErrors(['Please login to proceed.']);
            }
        }

        if ($exception instanceof PermissionDeniedException) {
            abort(403);
        }

        if ($exception instanceof NotFoundException) {
            abort(404);
        }

        if ($exception instanceof ValidationException) {
            return redirect()->back()->withInput()->withErrors($exception->getValidationErrors());
        }

        return $function();
    }

}