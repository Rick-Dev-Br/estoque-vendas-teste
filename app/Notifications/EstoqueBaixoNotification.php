<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EstoqueBaixoNotification extends Notification
{
    use Queueable;

    public function __construct(public $produtos) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo' => 'Produtos com estoque baixo',
            'produtos' => $this->produtos,
        ];
    }
}
