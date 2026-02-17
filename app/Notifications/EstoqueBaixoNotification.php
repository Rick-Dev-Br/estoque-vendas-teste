<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EstoqueBaixoNotification extends Notification
{
    use Queueable;

    public function __construct(public array $produtos, public string $tipo = 'estoque_baixo') {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $isEstoqueEsgotado = $this->tipo === 'estoque_esgotado';

        return [
            'tipo' => $this->tipo,
            'nivel' => $isEstoqueEsgotado ? 'alerta' : 'info',
            'titulo' => $isEstoqueEsgotado
                ? '⚠️ Produto sem estoque'
                : 'Produtos com estoque baixo',
            'descricao' => $isEstoqueEsgotado
                ? 'Um ou mais produtos acabaram e precisam de reposição imediata.'
                : 'Alguns produtos estão abaixo do estoque mínimo e precisam de atenção.',
            'produtos' => $this->produtos,
            'link' => route('notificacoes.index'),
        ];
    }
}
