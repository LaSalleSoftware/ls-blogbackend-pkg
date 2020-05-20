<?php

/**
 * This file is part of the Lasalle Software blog back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend;

// LaSalle Software
use Lasallesoftware\Blogbackend\Commands\BlogcustomseedCommand;

// Laravel classes
// https://github.com/laravel/framework/blob/5.6/src/Illuminate/Support/ServiceProvider.php
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Laravel Nova class
use Laravel\Nova\Nova;


class BlogbackendServiceProvider extends ServiceProvider
{
    use BlogbackendPoliciesServiceProvider;

    /**
     * Register any application services.
     *
     * "Within the register method, you should only bind things into the service container.
     * You should never attempt to register any event listeners, routes, or any other piece of functionality within
     * the register method. Otherwise, you may accidentally use a service that is provided by a service provider
     * which has not loaded yet."
     * (https://laravel.com/docs/5.6/providers#the-register-method(
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('lsblogbackend', function ($app) {
            return new LSBlogbackend();
        });

        $this->registerNovaResources();

        $this->registerArtisanCommands();
    }

    /**
     * Register the Nova resources for this package.
     *
     * Learned, somewhat the hard way, that Nova really wants its resources in one folder. This folder shall
     * be the Library's Nova resource folder.
     *
     * @return void
     */
    protected function registerNovaResources()
    {
        Nova::resources([
            \Lasallesoftware\Blogbackend\Nova\Resources\Category::class,
            \Lasallesoftware\Blogbackend\Nova\Resources\Tag::class,
            \Lasallesoftware\Blogbackend\Nova\Resources\Post::class,
            \Lasallesoftware\Blogbackend\Nova\Resources\Postupdate::class,
        ]);
    }

    /**
     * Register the artisan commands for this package.
     *
     * @return void
     */
    protected function registerArtisanCommands()
    {
        $this->app->bind('command.lsblogbackend:blogcustomseed', BlogcustomseedCommand::class);
        $this->commands([
            'command.lsblogbackend:blogcustomseed',
        ]);
    }



    /**
     * Bootstrap any package services.
     *
     * "So, what if we need to register a view composer within our service provider?
     * This should be done within the boot method. This method is called after all other service providers
     * have been registered, meaning you have access to all other services that have been registered by the framework"
     * (https://laravel.com/docs/5.6/providers)
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadMigrations();

        $this->loadRoutes();

        $this->loadTranslations();

        $this->registerPolicies();

        $this->registerFactories();

        $this->registerMiddleware($router);
    }

    /**
     * Load this package's migrations
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Load this package's routes
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    /**
     * Load this package's translations
     *
     * @return void
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../translations/', 'lasallesoftwareblogbackend');
    }

    /**
     * Load this package's factory location
     *
     * @return void
     */
    protected function registerFactories()
    {
        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(__DIR__ . '/../database/factories');
    }

    /**
     * Load this package's middleware
     *
     * @return void
     */
    protected function registerMiddleware($router)
    {
        $router->pushMiddlewareToGroup('jwt_auth', \Lasallesoftware\Blogbackend\JWT\Middleware\JWTMiddleware::class);
    }
}
