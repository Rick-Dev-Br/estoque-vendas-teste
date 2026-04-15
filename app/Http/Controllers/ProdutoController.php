<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::latest()->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:30|alpha_dash|unique:produtos,codigo',
            'codigo_barras' => 'nullable|string|max:50|unique:produtos,codigo_barras',
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'tipo_unidade' => 'required|in:unidade,saco,caixa,pacote',
            'unidade_medida' => 'required|in:un,kg,l,m',
            'unidade_quantidade' => 'required|numeric|min:0.01',
        ], [
            'codigo.required' => 'O código do produto é obrigatório.',
            'codigo.unique' => 'Já existe um produto com esse código.',
            'codigo_barras.unique' => 'Já existe um produto com esse código de barras.',
            'codigo_barras.max' => 'O código de barras deve ter no máximo 50 caracteres.',
            'nome.required' => 'O nome do produto é obrigatório.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.min' => 'O estoque não pode ser negativo.',
            'tipo_unidade.required' => 'O tipo de unidade é obrigatório.',
            'unidade_medida.required' => 'A unidade de medida é obrigatória.',
            'unidade_quantidade.required' => 'A quantidade por unidade é obrigatória.',
            'unidade_quantidade.min' => 'A quantidade por unidade deve ser maior que zero.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dados = $validator->validated();
        $dados['codigo_barras'] = isset($dados['codigo_barras'])
            ? trim((string) $dados['codigo_barras'])
            : null;
        $dados['codigo_barras'] = $dados['codigo_barras'] !== ''
            ? $dados['codigo_barras']
            : null;
        $dados['unidade_medida'] = strtolower($dados['unidade_medida']);
        $dados['tipo_unidade'] = strtolower($dados['tipo_unidade']);

        Produto::create($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

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

    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:30|alpha_dash|unique:produtos,codigo,' . $produto->id,
            'codigo_barras' => 'nullable|string|max:50|unique:produtos,codigo_barras,' . $produto->id,
            'nome' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'status' => 'required|in:ativo,inativo',
            'tipo_unidade' => 'required|in:unidade,saco,caixa,pacote',
            'unidade_medida' => 'required|in:un,kg,l,m',
            'unidade_quantidade' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dados = $validator->validated();
        $dados['codigo_barras'] = isset($dados['codigo_barras'])
            ? trim((string) $dados['codigo_barras'])
            : null;
        $dados['codigo_barras'] = $dados['codigo_barras'] !== ''
            ? $dados['codigo_barras']
            : null;
        $dados['tipo_unidade'] = strtolower($dados['tipo_unidade']);
        $dados['unidade_medida'] = strtolower($dados['unidade_medida']);

        $produto->update($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function toggleStatus(Produto $produto)
    {
        if ($produto->status === 'inativo') {
            if ($produto->estoque <= 0) {
                return redirect()->route('produtos.index')
                    ->with('error', 'O produto não pode ser ativado pois o estoque está zerado.');
            }

            $produto->status = 'ativo';
        } else {
            $produto->status = 'inativo';
        }

        $produto->save();

        return redirect()->route('produtos.index')
            ->with('success', 'Status do produto alterado com sucesso!');
    }

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

    public function buscarPorCodigoBarras(Request $request)
    {
        $codigoBarras = trim((string) $request->query('codigo_barras', ''));

        if ($codigoBarras === '') {
            return response()->json([
                'message' => 'Código de barras não informado.',
            ], 422);
        }

        $produto = Produto::query()
            ->where('codigo_barras', $codigoBarras)
            ->first();

        if (!$produto) {
            return response()->json([
                'message' => 'Nenhum produto encontrado para esse código de barras.',
            ], 404);
        }

        if ($produto->status !== 'ativo') {
            return response()->json([
                'message' => 'O produto está inativo e não pode ser vendido.',
            ], 422);
        }

        if ($produto->estoque <= 0) {
            return response()->json([
                'message' => 'O produto está sem estoque.',
            ], 422);
        }

        return response()->json([
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => (float) $produto->preco,
            'estoque' => (int) $produto->estoque,
            'codigo_barras' => $produto->codigo_barras,
            'label_venda' => sprintf(
                '%s - R$ %s (Estoque: %d)',
                $produto->nome,
                number_format((float) $produto->preco, 2, ',', '.'),
                $produto->estoque
            ),
        ]);
    }

    public function showJson(Produto $produto)
    {
        return response()->json([
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => (float) $produto->preco,
            'estoque' => (int) $produto->estoque,
            'status' => $produto->status,
            'codigo_barras' => $produto->codigo_barras,
        ]);
    }
}
