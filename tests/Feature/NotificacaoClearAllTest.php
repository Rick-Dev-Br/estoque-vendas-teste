<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class NotificacaoClearAllTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_consegue_limpar_notificacoes_pela_rota_delete(): void
    {

        $user = User::factory()->create();

        $user->notify(new class extends Notification {
            public function via($notifiable): array
            {
                return ['database'];
            }

            public function toDatabase($notifiable): array
            {
                return ['titulo' => 'Teste'];
            }
        });

        $this->actingAs($user)
            ->delete(route('notificacoes.clearAll'))
            ->assertRedirect(route('notificacoes.index'));

        $this->assertDatabaseCount('notifications', 0);
    }
}
