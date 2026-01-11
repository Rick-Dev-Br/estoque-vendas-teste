<?php

namespace App\Http\Controllers;

use App\Models\Produto; // IMPORTANTE: Adicionar esta linha
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::latest()->get();

        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
        ], [
            'nome.required' => 'O nome do produto é obrigatório.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.min' => 'O estoque não pode ser negativo.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        Produto::create($request->all());

        return redirect()->route('produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        $historico = $produto->vendaItens()
            ->select('venda_itens.*')
            ->join('vendas', 'vendas.id', '=', 'venda_itens.venda_id')
            ->where('vendas.status', 'pago')
            ->with(['venda.cliente'])
            ->orderByDesc('vendas.data_compra')
            ->orderByDesc('venda_itens.created_at')
            ->get();

        return view('produtos.show', compact('produto', 'historico'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
            'status' => 'required|in:ativo,inativo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $produto->update($request->all());

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function toggleStatus(Produto $produto)
    {
        $produto->status = $produto->status == 'ativo' ? 'inativo' : 'ativo';
        $produto->save();

        return redirect()->route('produtos.index')
            ->with('success', 'Status do produto alterado!');
    }

    /**
     * Display average sales per product.
     */
    public function mediaVendas()
    {
        $dados = Produto::query()
            ->leftJoin('venda_itens', 'produtos.id', '=', 'venda_itens.produto_id')
            ->selectRaw('produtos.id, produtos.nome, COALESCE(AVG(venda_itens.quantidade), 0) as media_quantidade')
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderByDesc('media_quantidade')
            ->get();

        return view('produtos.media_vendas', compact('dados'));
    }
}
