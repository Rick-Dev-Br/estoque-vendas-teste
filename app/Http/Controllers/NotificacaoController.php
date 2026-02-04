<?php

namespace App\Http\Controllers;

use App\Notifications\EstoqueBaixoNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificacaoController extends Controller
{
    public function estoqueBaixo(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'total' => 0,
                'notificacoes' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        $query = $usuario->unreadNotifications()
            ->where('type', EstoqueBaixoNotification::class);

        $notificacoes = $query
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notificacao) {
                return [
                    'id' => $notificacao->id,
                    'titulo' => $notificacao->data['titulo'] ?? 'Produtos com estoque baixo',
                    'produtos' => $notificacao->data['produtos'] ?? [],
                    'created_at' => optional($notificacao->created_at)->toDateTimeString(),
                ];
            })
            ->values();

        return response()->json([
            'total' => $query->count(),
            'notificacoes' => $notificacoes,
        ]);
    }
}
