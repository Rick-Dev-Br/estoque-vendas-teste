<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;

/**
 * Página inicial:
 * -
 * -
 */
Route::get('/', function () {
    return auth::check()
        ? view('welcome')
        : redirect()->route('login');
})->name('dashboard');

/**
 * Rotas de autenticação (login/registro/logout)
 */
Auth::routes();

/**
 * Rotas protegidas (login obrigatório)
 */
Route::middleware(['auth'])->group(function () {

    /**
     * Evita 404 do /home )
     */
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    //
    // PRODUTOS
    //


    Route::get('produtos/media-vendas', [ProdutoController::class, 'mediaVendas'])
        ->name('produtos.media_vendas');

    Route::resource('produtos', ProdutoController::class);

    Route::patch('produtos/{produto}/status', [ProdutoController::class, 'toggleStatus'])
        ->name('produtos.toggle-status');

    //
    // CLIENTES
    //
    Route::resource('clientes', ClienteController::class);

    Route::patch('clientes/{cliente}/status', [ClienteController::class, 'toggleStatus'])
        ->name('clientes.toggle-status');


    Route::get('vendas/historico', [VendaController::class, 'historico'])
        ->name('vendas.historico');

    Route::resource('vendas', VendaController::class);

    Route::patch('vendas/{venda}/status', [VendaController::class, 'alterarStatus'])
        ->name('vendas.alterar-status');

    Route::post('notificacoes/ler', function (Request $request) {
        $usuario = $request->user();
        if ($usuario) {
            $usuario->unreadNotifications->markAsRead();
        }

        return response()->noContent();
    })->name('notificacoes.ler');


    Route::get('/api/produtos/{id}', function ($id) {
        $produto = \App\Models\Produto::find($id);

        if (!$produto) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->json([
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'estoque' => $produto->estoque,
            'status' => $produto->status
        ]);
    });
});
