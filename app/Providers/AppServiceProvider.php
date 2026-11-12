<?php

namespace App\Providers;
 
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Webwizo\Shortcodes\Facades\Shortcode;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
 
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        schema::defaultStringLength(191);
        Paginator::useBootstrap();
        Shortcode::register('button', function($shortcode) {
            $class = $shortcode->class ?? 'dexp-btn-reflect';
            $text = $shortcode->getContent();
            return '<button class="' . $class . '">' . $text . '</button>';
        });
    }
}
