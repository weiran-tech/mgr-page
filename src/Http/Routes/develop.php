<?php

use Illuminate\Routing\Router;


Route::group([
    'namespace' => 'Weiran\MgrPage\Http\Request\Develop',
], function (Router $router) {
    /* Pam
     * ---------------------------------------- */
    $router->any('/', 'HomeController@index')
        ->name('wr-mgr-page:develop.home.cp');
    $router->any('optimize', 'HomeController@optimize')
        ->name('wr-mgr-page:develop.home.optimize');

    /* Env
     * ---------------------------------------- */
    $router->get('env/phpinfo', 'EnvController@phpinfo')
        ->name('wr-mgr-page:develop.env.phpinfo');
    $router->get('env/db', 'EnvController@db')
        ->name('wr-mgr-page:develop.env.db');

    /* Log
     * ---------------------------------------- */
    $router->any('log', 'LogController@index')
        ->name('wr-mgr-page:develop.log.index');
});
