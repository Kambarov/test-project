<?php

namespace App\Providers;

use App\Mixins\PaginationMixin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Builder::mixin(new PaginationMixin());
    }
}
