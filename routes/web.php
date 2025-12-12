<?php

use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::resource('produtos', ProdutoController::class);


Route::patch('produtos/{produto}/status', [ProdutoController::class, 'toggleStatus'])
    ->name('produtos.toggle-status');


Route::resource('clientes', ClienteController::class);


Route::patch('clientes/{cliente}/status', [ClienteController::class, 'toggleStatus'])
    ->name('clientes.toggle-status');


Route::resource('vendas', VendaController::class);


Route::patch('vendas/{venda}/status', [VendaController::class, 'alterarStatus'])
    ->name('vendas.alterar-status');


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
