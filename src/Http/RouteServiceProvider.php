<?php

namespace Weiran\MgrPage\Http;

use Illuminate\Routing\Router;
use Route;

class RouteServiceProvider extends \Weiran\Framework\Application\RouteServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            $this->mapBackendRoutes();

            $this->mapDevRoutes();
        });
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapBackendRoutes(): void
    {
        // backend
        Route::group([
            'prefix'    => $this->prefix,
            'namespace' => 'Weiran\MgrPage\Http\Request\Backend',
        ], function (Router $router) {
            $router->any('/', 'HomeController@index')
                ->middleware('backend-auth')
                ->name('weiran-mgr-page:backend.home.index');
            $router->any('login', 'HomeController@login')
                ->middleware('web')
                ->name('weiran-mgr-page:backend.home.login');
            $router->any('captcha/send', 'CaptchaController@send')
                ->middleware('web')
                ->name('weiran-mgr-page:backend.captcha.send');
        });

        Route::group([
            'prefix'     => $this->prefix . '/system',
            'middleware' => 'backend-auth',
        ], function () {
            require_once __DIR__ . '/Routes/backend.php';
        });
    }

    /**
     * Define the "web" routes for the module.
     * These routes all receive session state, CSRF protection, etc.
     * @return void
     */
    protected function mapDevRoutes(): void
    {
        // develop
        Route::group([
            'middleware' => 'web',
            'prefix'     => $this->prefix . '/develop',
        ], function (Router $router) {
            $router->any('api/json/{type?}', 'Poppy\MgrPage\Http\Request\Develop\ApiController@json')
                ->name('weiran-mgr-page:develop.api.json');
        });
        Route::group([
            'middleware' => 'backend-auth',
            'prefix'     => $this->prefix . '/develop',
        ], function () {
            require_once __DIR__ . '/Routes/develop.php';
        });
    }
}