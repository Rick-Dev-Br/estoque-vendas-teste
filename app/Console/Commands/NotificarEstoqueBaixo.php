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
            ->get(['id', 'nome', 'estoque', 'estoque_minimo']);

        if ($produtos->isEmpty()) {
            $this->info('Nenhum produto com estoque baixo.');

            return self::SUCCESS;
        }

        $produtosBaixos = $produtos->where('estoque', '>', 0)->values()->toArray();
        $produtosEsgotados = $produtos->where('estoque', '<=', 0)->values()->toArray();

        $admins = User::all();

        foreach ($admins as $user) {
            if (!empty($produtosBaixos)) {
                $user->notify(new EstoqueBaixoNotification($produtosBaixos, 'estoque_baixo'));
            }

            if (!empty($produtosEsgotados)) {
                $user->notify(new EstoqueBaixoNotification($produtosEsgotados, 'estoque_esgotado'));
            }
        }

        $this->info('Notificações enviadas.');

        return self::SUCCESS;
    }
}
