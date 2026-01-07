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

        $admins = User::all();
        foreach ($admins as $user) {
            $user->notify(new EstoqueBaixoNotification($produtos->toArray()));
        }

        $this->info('Notificações enviadas.');
        return self::SUCCESS;
    }
}
