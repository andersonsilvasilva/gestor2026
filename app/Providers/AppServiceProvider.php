<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rules\Password;
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

        // Política de senha única para todo o app (convite de conta, "esqueci
        // minha senha" do Fortify e troca de senha no Perfil): mínimo 8
        // caracteres, com maiúscula, minúscula e número. Sem máximo artificial.
        Password::defaults(function () {
            return Password::min(8)->mixedCase()->numbers();
        });

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
