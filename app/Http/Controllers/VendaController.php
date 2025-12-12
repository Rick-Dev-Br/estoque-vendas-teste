<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\VendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendas = Venda::with('cliente')->latest()->get();
        return view('vendas.index', compact('vendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::ativo()->get();
        $produtos = Produto::where('estoque', '>', 0)->get();

        return view('vendas.create', compact('clientes', 'produtos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $total = 0;
            $itensData = [];

            foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);

                if (!$produto->podeVender($item['quantidade'])) {
                    throw new \Exception(
                        "Produto {$produto->nome} não pode ser vendido." .
                        "Verifique status e estoque."
                    );
                }

                $subtotal = $produto->preco * $item['quantidade'];
                $total += $subtotal;

                $itensData[] = [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco,
                    'subtotal' => $subtotal
                ];
            }

            $venda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'total' => $total,
                'status' => 'pendente'
            ]);

            foreach ($itensData as $itemData) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario']
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.index')
                ->with('success', 'Venda criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'itens.produto']);

        return view('vendas.show', compact('venda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        if ($venda->status != 'pendente') {
            return redirect()->route('vendas.index')
                ->with('error', 'Apenas vendas pendentes podem ser editadas.');
        }

        $clientes = Cliente::ativo()->get();
        $produtos = Produto::ativo()->comEstoque()->get();

        $venda->load('itens');

        return view('vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        if ($venda->status != 'pendente') {
            return redirect()->route('vendas.index')
                ->with('error', 'Não é possivel editar esta venda.');
        }

        $validator =  Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $venda->itens()->delete();

            $total = 0;
            $itensData = [];

            foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);

                if (!$produto->podeVender($item['quantidade'])) {
                    throw new \Exception(
                        "Produto {$produto->nome} não pode ser vendido."
                    );
                }

                $subtotal = $produto->preco * $item['quantidade'];
                $total += $subtotal;

                $itensData[] = [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco
                ];
            }

            $venda->update([
                'cliente_id' => $request->cliente_id,
                'total' => $total
            ]);

            foreach ($itensData  as $itemData) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario']
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.index')
                ->with('success', 'Venda atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        if ($venda->status != 'pendente') {
            return redirect()->route('vendas.index')
                ->with('error', 'Apenas vendas pendentes podem ser excluídas.');
        }

        $venda->delete();

        return redirect()->route('vendas.index')
            ->with('success', 'Venda excluída com sucesso!');
    }

    public function alterarStatus(Request $request, Venda $venda)
    {
        $request->validate([
            'status' => 'required|in:pago,cancelado'
        ]);

        try {
            $venda->mudarStatus($request->status);

            return redirect()->route('vendas.index')
                ->with('success', 'Status da venda alterado para: ' . $venda->status_formatado);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
