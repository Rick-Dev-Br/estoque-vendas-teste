<?php

namespace App\Providers;

use App\Models\Produto;
use App\Models\User;
use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
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
        if (config('session.driver') === 'database' && !Schema::hasTable('sessions')) {
            config(['session.driver' => 'file']);
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function () {
            $usuario = Auth::user();

            if (!$usuario instanceof User) {
                return;
            }

            if (!Schema::hasTable('produtos') || !Schema::hasTable('notifications')) {
                return;
            }

            $produtosBaixoEstoque = Cache::remember('produtos.estoque_baixo', now()->addMinute(), function () {
                return Produto::whereColumn('estoque', '<=', 'estoque_minimo')
                    ->where('status', 'ativo')
                    ->get(['id', 'nome', 'estoque', 'estoque_minimo']);
            });

            if ($produtosBaixoEstoque->isEmpty()) {
                return;
            }

                $cacheKey = "notificacoes.estoque_baixo.{$usuario->id}";
            $notificacaoRecente = Cache::remember($cacheKey, now()->addMinute(), function () use ($usuario) {
                return $usuario->notifications()
                    ->where('type', EstoqueBaixoNotification::class)
                    ->where('created_at', '>=', now()->subMinutes(30))
                    ->exists();
            });

            if (!$notificacaoRecente) {
                $usuario->notify(new EstoqueBaixoNotification($produtosBaixoEstoque->toArray()));
                Cache::put($cacheKey, true, now()->addMinutes(30));
            }
        });
    }
}
