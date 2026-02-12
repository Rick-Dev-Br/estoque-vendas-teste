<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificacaoController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $filtro = $request->string('filtro')->toString();

        $query = $usuario->notifications()->latest();

        if ($filtro === 'nao_lidas') {
            $query->whereNull('read_at');
        }

        if ($filtro === 'lidas') {
            $query->whereNotNull('read_at');
        }

        $notificacoes = $query->paginate(15)->withQueryString();

        return view('notificacoes.index', [
            'notificacoes' => $notificacoes,
            'filtro' => in_array($filtro, ['nao_lidas', 'lidas'], true) ? $filtro : 'todas',
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $usuario = $request->user();

        if (!$notificacao->read_at) {
            $notificacao->markAsRead();
        }

        return back()->with('success', 'Notificação marcada como lida.');
    }

        public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

        public function destroy(Request $request, string $id): RedirectResponse
    {
        $notificacao = $this->findUserNotification($request, $id);
        $notificacao->delete();

        return back()->with('success', 'Notificação apagada com sucesso.');
    }

    public function clearAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('success', 'Todas as notificações foram removidas.');
    }

    protected function findUserNotification(Request $request, string $id): DatabaseNotification
    {
        return $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();
    }
}
