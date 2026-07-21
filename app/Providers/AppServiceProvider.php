<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use League\CommonMark\Extension\Footnote\Event\GatherFootnotesListener;
use App\Models\User;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        //-------------
        // GATES
        //-------------
        Gate::define('admin', function($user){
          return $user->role === 'admin';
        });

        Gate::define('rh', function(){
            return auth()->user()->role === 'rh'; 
          });
        
         Gate::define('tp', function(){
            return auth()->user()->role === 'tp';
          });

        // Departamentos, Procedimentos e Colaboradores: admin e Responsável Técnico
        Gate::define('manage-catalog', function ($user) {
            return in_array($user->role, ['admin', 'rt']);
        });

        // Ver dados de todos os colaboradores no Dashboard e escolher
        // qualquer colaborador no gerador de relatórios
        Gate::define('view-all-data', function ($user) {
            return in_array($user->role, ['admin', 'rt', 'sec']);
        });
    }
}
