<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificacaoController extends Controller
{
    public function estoqueBaixo(Request $request): JsonResponse
    {
        $produtos = Produto::estoqueBaixo()
            ->orderBy('estoque')
            ->limit(10)
            ->get(['id', 'nome', 'estoque', 'estoque_minimo']);

        return response()->json([
            'total' => Produto::estoqueBaixo()->count(),
            'produto' => $produtos,
        ]);
    }
}
