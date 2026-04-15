<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Cache;

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
        $produtosAlertasAtuais = Produto::query()
            ->estoqueBaixo()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        return view('notificacoes.index', [
            'notificacoes' => $notificacoes,
            'filtro' => in_array($filtro, ['nao_lidas', 'lidas'], true) ? $filtro : 'todas',
            'produtosAlertasAtuais' => $produtosAlertasAtuais,
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notificacao = $this->findUserNotification($request, $id);

        if (!$notificacao->read_at) {
            $notificacao->markAsRead();
        }

        $this->limparCacheNotificacoes($request);

        return back()->with('success', 'Notificação marcada como lida.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        $this->limparCacheNotificacoes($request);

        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $notificacao = $this->findUserNotification($request, $id);
        $notificacao->delete();

        $this->limparCacheNotificacoes($request);

        return back()->with('success', 'Notificação apagada com sucesso.');
    }

    public function clearAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();
        $this->limparCacheNotificacoes($request);

        return redirect()
            ->route('notificacoes.index')
            ->with('success', 'Todas as notificações foram removidas.');
    }

    protected function findUserNotification(Request $request, string $id): DatabaseNotification
    {
        return $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();
    }

    private function limparCacheNotificacoes(Request $request): void
    {
        $userId = $request->user()->id;

        Cache::forget("notificacoes.lista.{$userId}");
        Cache::forget("notificacoes.nao_lidas.{$userId}");
    }
}
