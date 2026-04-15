<?php

namespace App\Console\Commands;

use App\Models\Produto;
use App\Models\User;
use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NotificarEstoqueBaixo extends Command
{
    protected $signature = 'estoque:notificar-baixo';
    protected $description = 'Notifica quando produtos estiverem com estoque baixo';

    public function handle(): int
    {
        $produtos = Produto::query()
            ->estoqueBaixo()
            ->get(['id', 'nome', 'estoque', 'estoque_minimo']);

        $produtosBaixos = $produtos->where('estoque', '>', 0)->values()->toArray();
        $produtosEsgotados = $produtos->where('estoque', '<=', 0)->values()->toArray();

        $usuarios = User::all();

        foreach ($usuarios as $user) {
            $this->notificarSeMudou($user, $produtosBaixos, 'estoque_baixo');
            $this->notificarSeMudou($user, $produtosEsgotados, 'estoque_esgotado');
        }

        $this->info(
            $produtos->isEmpty()
                ? 'Nenhum produto com estoque baixo. Alertas abertos sincronizados.'
                : 'Verificação de notificações concluída.'
        );

        return self::SUCCESS;
    }

    private function notificarSeMudou(User $user, array $produtos, string $tipo): void
    {
        $cacheKey = "notificacoes.{$tipo}.{$user->id}";
        $assinaturaAtual = $this->gerarAssinaturaProdutos($produtos);
        $notificacoesAbertas = $this->obterNotificacoesAbertas($user, $tipo);

        if ($assinaturaAtual === null) {
            $notificacoesAbertas->each->delete();
            Cache::forget($cacheKey);
            $this->limparCachesUsuario($user);

            return;
        }

        $notificacoesComAssinaturaAtual = $notificacoesAbertas
            ->filter(fn ($notification) => $this->gerarAssinaturaProdutos($notification->data['produtos'] ?? []) === $assinaturaAtual)
            ->values();

        $notificacoesAbertas
            ->reject(fn ($notification) => $this->gerarAssinaturaProdutos($notification->data['produtos'] ?? []) === $assinaturaAtual)
            ->each
            ->delete();

        if ($notificacoesComAssinaturaAtual->count() > 1) {
            $notificacoesComAssinaturaAtual
                ->slice(1)
                ->each
                ->delete();
        }

        if ($notificacoesComAssinaturaAtual->isEmpty()) {
            $user->notify(new EstoqueBaixoNotification($produtos, $tipo));
        }

        Cache::put($cacheKey, $assinaturaAtual, now()->addHours(6));
        $this->limparCachesUsuario($user);
    }

    private function obterNotificacoesAbertas(User $user, string $tipo): Collection
    {
        return $user->unreadNotifications()
            ->where('type', EstoqueBaixoNotification::class)
            ->latest()
            ->get()
            ->filter(function ($notification) use ($tipo) {
                return ($notification->data['tipo'] ?? 'estoque_baixo') === $tipo;
            })
            ->values();
    }

    private function gerarAssinaturaProdutos(array $produtos): ?string
    {
        if (empty($produtos)) {
            return null;
        }

        return md5(
            collect($produtos)
                ->map(function (array $produto) {
                    return [
                        'id' => $produto['id'] ?? null,
                        'estoque' => $produto['estoque'] ?? null,
                        'estoque_minimo' => $produto['estoque_minimo'] ?? null,
                    ];
                })
                ->sortBy('id')
                ->values()
                ->toJson()
        );
    }

    private function limparCachesUsuario(User $user): void
    {
        Cache::forget("notificacoes.lista.{$user->id}");
        Cache::forget("notificacoes.nao_lidas.{$user->id}");
    }
}
