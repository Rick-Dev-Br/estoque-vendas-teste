<?php

namespace App\Providers;

use App\Models\Produto;
use App\Models\User;
use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Notifications\DatabaseNotification;
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

        View::composer('layouts.app', function ($view) {
            $usuario = Auth::user();
            $notificacoes = collect();
            $notificacoesNaoLidas = 0;

            if (!$usuario instanceof User || !Schema::hasTable('notifications')) {
                $view->with([
                    'notificacoes' => $notificacoes,
                    'notificacoesNaoLidas' => $notificacoesNaoLidas,
                ]);

                return;
            }

            if (Schema::hasTable('produtos')) {
                $produtosBaixoEstoque = Cache::remember('produtos.estoque_baixo', now()->addMinute(), function () {
                    return Produto::whereColumn('estoque', '<=', 'estoque_minimo')
                        ->where('status', 'ativo')
                        ->get(['id', 'nome', 'estoque', 'estoque_minimo']);
                });

                if ($produtosBaixoEstoque->isNotEmpty()) {
                    $cacheKey = "notificacoes.estoque_baixo.{$usuario->id}";
                    $assinaturaAtual = md5($produtosBaixoEstoque
                        ->map(fn ($produto) => [
                            $produto->id,
                            $produto->estoque,
                            $produto->estoque_minimo,
                        ])
                        ->values()
                        ->toJson());
                    $assinaturaAnterior = Cache::get($cacheKey);

                    if ($assinaturaAnterior !== $assinaturaAtual) {
                        $usuario->notify(new EstoqueBaixoNotification($produtosBaixoEstoque->toArray()));
                        Cache::put($cacheKey, $assinaturaAtual, now()->addHours(6));
                        Cache::forget("notificacoes.lista.{$usuario->id}");
                        Cache::forget("notificacoes.nao_lidas.{$usuario->id}");
                    }
                }
            }

            $notificacoes = Cache::remember("notificacoes.lista.{$usuario->id}", now()->addSeconds(30), function () use ($usuario) {
                return $usuario->notifications()
                    ->latest()
                    ->take(5)
                    ->get(['id', 'type', 'data', 'read_at', 'created_at']);
            });

            $notificacoesNaoLidas = Cache::remember("notificacoes.nao_lidas.{$usuario->id}", now()->addSeconds(30), function () use ($usuario) {
                return $usuario->unreadNotifications()->count();
            });

            $view->with([
                'notificacoes' => $notificacoes,
                'notificacoesNaoLidas' => $notificacoesNaoLidas,
            ]);
        });

        DatabaseNotification::deleting(function (DatabaseNotification $notification) {
            if ($notification->notifiable_type === User::class && $notification->notifiable_id) {
                Cache::forget("notificacoes.lista.{$notification->notifiable_id}");
                Cache::forget("notificacoes.nao_lidas.{$notification->notifiable_id}");
            }
        });

        DatabaseNotification::saved(function (DatabaseNotification $notification) {
            if ($notification->notifiable_type === User::class && $notification->notifiable_id) {
                Cache::forget("notificacoes.lista.{$notification->notifiable_id}");
                Cache::forget("notificacoes.nao_lidas.{$notification->notifiable_id}");
            }
        });
    }
}
