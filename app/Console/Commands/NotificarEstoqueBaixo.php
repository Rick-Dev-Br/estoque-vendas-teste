<?php

namespace App\Console\Commands;

use App\Models\Produto;
use App\Models\User;
use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Console\Command;

class NotificarEstoqueBaixo extends Command
{
    protected $signature = 'estoque:notificar-baixo';
    protected $description = 'Notifica quando produtos estiverem com estoque baixo';

    public function handle(): int
    {
        $produtos = Produto::whereColumn('estoque', '<=', 'estoque_minimo')
            ->where('status', 'ativo')
            ->get(['id','nome','estoque','estoque_minimo']);

        if ($produtos->isEmpty()) {
            $this->info('Nenhum produto com estoque baixo.');
            return self::SUCCESS;
        }

        $produtosData = $produtos->map(fn ($produto) => [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'estoque' => $produto->estoque,
            'estoque_minimo' => $produto->estoque_minimo,
        ])->values()->toArray();

        $admins = User::all();
        foreach ($admins as $user) {
            $ultimaNotificacao = $user->notifications()
                ->where('type', EstoqueBaixoNotification::class)
                ->latest()
                ->first();

            $jaNotificado = $ultimaNotificacao
                && ($ultimaNotificacao->data['produtos'] ?? []) === $produtosData;

            if ($jaNotificado) {
                continue;
            }

            $user->notify(new EstoqueBaixoNotification($produtosData));
        }

        $this->info('Notificações enviadas.');
        return self::SUCCESS;
    }
}
