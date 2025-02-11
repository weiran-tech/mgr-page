<?php

declare(strict_types = 1);

namespace Weiran\MgrPage;


use Illuminate\Console\Scheduling\Schedule;
use Weiran\Framework\Events\PoppySchedule;
use Weiran\Framework\Exceptions\ModuleNotFoundException;
use Weiran\Framework\Support\WeiranServiceProvider;
use Weiran\MgrPage\Classes\FormBuilder;
use Weiran\MgrPage\Commands\MixCommand;

/**
 * @property $listens;
 */
class ServiceProvider extends WeiranServiceProvider
{

    /**
     * Bootstrap the module services.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot()
    {
        parent::boot('poppy.mgr-page');

        // 注册 api 文档配置
        $this->publishes([
            __DIR__ . '/../resources/views/vendor/pagination-layui.blade.php' => resource_path('views/vendor/pagination/layui.blade.php'),
        ], 'poppy');
        $this->publishes([
            // 需要从项目中反向复制的页面
            __DIR__ . '/../resources/libs/boot/app.min.js'        => public_path('assets/libs/boot/app.min.js'),
            __DIR__ . '/../resources/libs/boot/style.css'         => public_path('assets/libs/boot/style.css'),
            // editor
            __DIR__ . '/../resources/libs/boot/wangeditor@5.1.js' => public_path('assets/libs/boot/wangeditor@5.1.js'),
            // 编辑器
            __DIR__ . '/../resources/libs/jquery/backstretch'     => public_path('assets/libs/jquery/backstretch'),
            __DIR__ . '/../resources/libs/vue'                    => public_path('assets/libs/vue'),
            __DIR__ . '/../resources/libs/easy-web/'              => public_path('assets/libs/easy-web/'),
            __DIR__ . '/../resources/libs/jquery/data-tables/'    => public_path('assets/libs/jquery/data-tables/'),
            __DIR__ . '/../resources/libs/layui/'                 => public_path('assets/libs/layui/'),
            __DIR__ . '/../resources/images/'                     => public_path('assets/images/'),
            __DIR__ . '/../resources/font/'                       => public_path('assets/font/'),
        ], 'poppy-mix');
    }

    /**
     * Register the module services.
     * @return void
     */
    public function register()
    {
        $this->app->register(Http\MiddlewareServiceProvider::class);
        $this->app->register(Http\RouteServiceProvider::class);

        $this->registerConsole();

        $this->registerSchedule();

        $this->registerForm();
    }

    public function provides(): array
    {
        return [
            'poppy.mgr-page.form',
        ];
    }

    private function registerSchedule(): void
    {
        app('events')->listen(PoppySchedule::class, function (Schedule $schedule) {

        });
    }

    private function registerConsole(): void
    {
        // system
        $this->commands([
            MixCommand::class,
        ]);
    }

    private function registerForm(): void
    {

        $this->app->singleton('poppy.mgr-page.form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }
}