<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Weiran\MgrPage\Http\Request\Backend',
], function (Router $router) {
    $router->any('cp', 'HomeController@cp')
        ->name('weiran-mgr-page:backend.home.cp');
    $router->any('password', 'HomeController@password')
        ->name('weiran-mgr-page:backend.home.password');
    $router->any('clear_cache', 'HomeController@clearCache')
        ->name('weiran-mgr-page:backend.home.clear_cache');
    $router->any('logout', 'HomeController@logout')
        ->name('weiran-mgr-page:backend.home.logout');
    $router->any('setting/{path?}/{index?}', 'HomeController@setting')
        ->name('weiran-mgr-page:backend.home.setting');
    $router->any('easy-web/{type}', 'HomeController@easyWeb')
        ->name('weiran-mgr-page:backend.home.easy-web');

    $router->get('role', 'RoleController@index')
        ->name('weiran-mgr-page:backend.role.index');
    $router->any('role/establish/{id?}', 'RoleController@establish')
        ->name('weiran-mgr-page:backend.role.establish');
    $router->any('role/delete/{id?}', 'RoleController@delete')
        ->name('weiran-mgr-page:backend.role.delete');
    $router->any('role/menu/{id}', 'RoleController@menu')
        ->name('weiran-mgr-page:backend.role.menu');

    $router->get('pam', 'PamController@index')
        ->name('weiran-mgr-page:backend.pam.index');
    $router->any('pam/establish/{id?}', 'PamController@establish')
        ->name('weiran-mgr-page:backend.pam.establish');
    $router->any('pam/password/{id}', 'PamController@password')
        ->name('weiran-mgr-page:backend.pam.password');
    $router->any('pam/note/{id}', 'PamController@note')
        ->name('weiran-mgr-page:backend.pam.note');
    $router->any('pam/disable/{id}', 'PamController@disable')
        ->name('weiran-mgr-page:backend.pam.disable');
    $router->any('pam/enable/{id}', 'PamController@enable')
        ->name('weiran-mgr-page:backend.pam.enable');
    $router->any('pam/mobile/{id}', 'PamController@mobile')
        ->name('weiran-mgr-page:backend.pam.mobile');
    $router->any('pam/clearMobile/{id}', 'PamController@clearMobile')
        ->name('weiran-mgr-page:backend.pam.clear_mobile');
    $router->any('pam/log', 'PamController@log')
        ->name('weiran-mgr-page:backend.pam.log');
    $router->any('pam/setting_log', 'PamController@settingLog')
        ->name('weiran-mgr-page:backend.pam.setting_log');
    $router->any('pam/token', 'PamController@token')
        ->name('weiran-mgr-page:backend.pam.token');
    $router->any('pam/ban/{id}/{type}', 'PamController@ban')
        ->name('weiran-mgr-page:backend.pam.ban');
    $router->any('pam/delete_token/{id}', 'PamController@deleteToken')
        ->name('weiran-mgr-page:backend.pam.delete_token');
    $router->any('pam/setting/{id}', 'PamController@setting')
        ->name('weiran-mgr-page:backend.pam.setting');

    $router->any('ban', 'BanController@index')
        ->name('weiran-mgr-page:backend.ban.index');
    $router->any('ban/establish/{id?}', 'BanController@establish')
        ->name('weiran-mgr-page:backend.ban.establish');
    $router->any('ban/setting', 'BanController@setting')
        ->name('weiran-mgr-page:backend.ban.setting');
    $router->any('ban/status', 'BanController@status')
        ->name('weiran-mgr-page:backend.ban.status');
    $router->any('ban/type', 'BanController@type')
        ->name('weiran-mgr-page:backend.ban.type');
    $router->any('ban/delete/{id}', 'BanController@delete')
        ->name('weiran-mgr-page:backend.ban.delete');

    /* 发送测试邮件
     * ---------------------------------------- */
    $router->any('mail/store', 'MailController@store')
        ->name('weiran-mgr-page:backend.mail.store');
    $router->any('mail/test', 'MailController@test')
        ->name('weiran-mgr-page:backend.mail.test');

    $router->any('upload/store', 'UploadController@store')
        ->name('weiran-mgr-page:backend.upload.store');
});