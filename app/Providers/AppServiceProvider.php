<?php

namespace App\Providers;

use App\Models\User;
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
