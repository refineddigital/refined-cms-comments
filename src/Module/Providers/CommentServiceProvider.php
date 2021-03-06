<?php

namespace RefinedDigital\Comments\Module\Providers;

use Illuminate\Support\ServiceProvider;
use RefinedDigital\Comments\Commands\Install;
use RefinedDigital\CMS\Modules\Core\Aggregates\PackageAggregate;
use RefinedDigital\CMS\Modules\Core\Aggregates\ModuleAggregate;
use RefinedDigital\CMS\Modules\Core\Aggregates\RouteAggregate;

class CommentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->addNamespace('comments', [
            base_path().'/resources/views',
            __DIR__.'/../Resources/views',
        ]);

        try {
            if ($this->app->runningInConsole()) {
                if (\DB::connection()->getDatabaseName() && !\Schema::hasTable('comments')) {
                    $this->commands([
                        Install::class
                    ]);
                }
            }
        } catch (\Exception $e) {}

        $this->publishes([
            __DIR__.'/../../../config/comments.php' => config_path('comments.php'),
        ], 'comments');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        app(RouteAggregate::class)
            ->addRouteFile('comments', __DIR__.'/../Http/routes.php');

        $menuConfig = [
            'order' => 210,
            'name' => 'Comments',
            'icon' => 'fas fa-comments',
            'route' => 'comments',
            'activeFor' => ['comments']
        ];

        app(ModuleAggregate::class)
            ->addMenuItem($menuConfig);

        app(PackageAggregate::class)
            ->addPackage('Comments', [
                'repository' => \RefinedDigital\Comments\Module\Http\Repositories\CommentRepository::class,
                'model' => '\\RefinedDigital\\Comments\\Module\\Models\\Comment',
            ]);
    }
}
