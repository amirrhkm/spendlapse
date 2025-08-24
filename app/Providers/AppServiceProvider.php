<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(
            \App\Contracts\TransactionParserInterface::class,
            \App\Services\CsvTransactionParser::class
        );

        $this->app->bind(
            \App\Contracts\TransactionRepositoryInterface::class,
            \App\Repositories\TransactionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
