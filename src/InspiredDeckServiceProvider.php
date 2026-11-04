<?php

namespace MBLSolutions\InspiredDeckLaravel;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use MBLSolutions\InspiredDeck\Exceptions\AuthenticationException as ApiAuthenticationException;
use MBLSolutions\InspiredDeck\Exceptions\NotFoundException;
use MBLSolutions\InspiredDeck\Exceptions\PermissionDeniedException;
use MBLSolutions\InspiredDeck\Exceptions\ValidationException;
use MBLSolutions\InspiredDeck\InspiredDeck;
use MBLSolutions\InspiredDeckLaravel\Middleware\LoadInspiredDeckConfig;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Response;

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
     * @param $exception
     * @param callable|null $function
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public static function exceptionHandling($request, $exception, ?callable $function = null)
    {
        if (route_contains('async') || route_contains('api')) {
            if ($exception instanceof ValidationException) {
                return Response::json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getValidationErrors()
                ], $exception->getCode());
            }
        }

        if ($exception instanceof HttpException) {
            if ($exception->getStatusCode() === 401) {
                return static::loginRequired($request);
            }
        }

        // The API token has ended or is no longer valid
        if ($exception instanceof ApiAuthenticationException && $exception->getCode() === 401) {
            Session::forget(config('inspireddeck.session'));

            return static::loginRequired($request);
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

    /**
     * Send the user to the login page; JSON requests get a 401 with the login URL
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    protected static function loginRequired($request)
    {
        if ($request instanceof Request && $request->expectsJson()) {
            return Response::json([
                'message' => 'Please login to proceed.',
                'redirect' => route('login'),
            ], 401);
        }

        return redirect()->route('login')->withErrors(['Please login to proceed.']);
    }

}
