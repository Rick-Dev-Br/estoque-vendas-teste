<?php

namespace App\Providers;

use App\Models\Produto;
use App\Models\User;
use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function () {
            $usuario = Auth::user();

            if (!$usuario instanceof User) {
                return;
            }

            $produtosBaixoEstoque = Produto::whereColumn('estoque', '<=', 'estoque_minimo')
                ->where('status', 'ativo')
                ->get(['id', 'nome', 'estoque', 'estoque_minimo']);

            if ($produtosBaixoEstoque->isEmpty()) {
                return;
            }

            $notificacaoRecente = $usuario->notifications()
                ->where('type', EstoqueBaixoNotification::class)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->exists();

            if (!$notificacaoRecente) {
                $usuario->notify(new EstoqueBaixoNotification($produtosBaixoEstoque->toArray()));
            }
        });
    }
}
