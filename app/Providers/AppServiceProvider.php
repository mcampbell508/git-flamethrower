<?php

declare(strict_types=1);

namespace Shopworks\Git\Review\Providers;

use Illuminate\Support\ServiceProvider;
use Shopworks\Git\Review\Commands\ESLint\Command as ESLintCommand;
use Shopworks\Git\Review\Yml\YmlConfiguration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(YmlConfiguration::class, function () {
            return new YmlConfiguration(\getcwd());
        });

        $this->commands([
            ESLintCommand::class,
        ]);
    }
}
