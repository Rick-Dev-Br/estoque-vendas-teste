<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? view('welcome')
        : redirect()->route('login');
})->name('dashboard');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('produtos/media-vendas', [ProdutoController::class, 'mediaVendas'])
        ->name('produtos.media_vendas');

    Route::get('api/produtos/por-codigo-barras', [ProdutoController::class, 'buscarPorCodigoBarras'])
        ->name('produtos.buscar-por-codigo-barras');

    Route::get('api/produtos/{produto}', [ProdutoController::class, 'showJson'])
        ->name('produtos.show-json');

    Route::resource('produtos', ProdutoController::class);

    Route::patch('produtos/{produto}/status', [ProdutoController::class, 'toggleStatus'])
        ->name('produtos.toggle-status');

    Route::resource('clientes', ClienteController::class);

    Route::patch('clientes/{cliente}/status', [ClienteController::class, 'toggleStatus'])
        ->name('clientes.toggle-status');

    Route::get('vendas/caixa', [VendaController::class, 'caixa'])
        ->name('vendas.caixa');

    Route::get('vendas/historico', [VendaController::class, 'historico'])
        ->name('vendas.historico');

    Route::resource('vendas', VendaController::class);

    Route::patch('vendas/{venda}/status', [VendaController::class, 'alterarStatus'])
        ->name('vendas.alterar-status');

    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('index');
        Route::patch('/{id}/ler', [NotificacaoController::class, 'markAsRead'])->name('markAsRead');
        Route::patch('/marcar-todas', [NotificacaoController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::delete('/limpar', [NotificacaoController::class, 'clearAll'])->name('clearAll');
        Route::delete('/{id}', [NotificacaoController::class, 'destroy'])->name('destroy');
    });
});
